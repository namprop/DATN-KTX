<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\Paymentsuccessful;
use App\Models\ParentStudent;
use App\Models\Student;

class VnpayController extends Controller
{
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'integer', 'exists:payments,payment_id'],
        ]);

        $vnp_TmnCode    = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url        = config('vnpay.vnp_Url');
        $vnp_ReturnUrl  = config('vnpay.vnp_ReturnUrl');

        $payment = Payment::with('student')->find($validated['payment_id']);
        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        if (!$this->canPay($request, $payment)) {
            abort(403, 'Bạn không có quyền thanh toán hóa đơn này.');
        }

        if ($payment->payment_status !== PaymentStatus::Unpaid) {
            return response()->json([
                'status' => false,
                'message' => 'Hóa đơn không ở trạng thái có thể thanh toán.',
            ], 422);
        }

        $vnp_TxnRef = $payment->payment_code;
        $vnp_OrderInfo = 'Thanh toán hóa đơn tháng ' . $payment->month . '/' . $payment->year;
        $vnp_Amount = $payment->total_amount * 100;
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . http_build_query($inputData) . '&vnp_SecureHash=' . $vnpSecureHash;


        return response()->json([
            'payment_url' => $vnp_Url,
        ]);
    }

    private function canPay(Request $request, Payment $payment): bool
    {
        $user = $request->user();

        if (!$user || !$payment->student) {
            return false;
        }

        if (strcasecmp($user->role, 'Student') === 0) {
            return Student::where('user_id', $user->id)
                ->whereKey($payment->student_id)
                ->exists();
        }

        if (in_array(strtolower($user->role), ['parent', 'parentstudent'], true)) {
            return ParentStudent::where('user_id', $user->id)
                ->where('student_id', $payment->student_id)
                ->exists();
        }

        return false;
    }

    public function vnpReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        if (!$vnp_HashSecret || !$vnp_SecureHash) {
            Log::warning('VNPay callback is missing its secret or signature.');

            return $this->paymentRedirect(false);
        }

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if (!hash_equals($secureHash, $vnp_SecureHash)) {
            Log::warning('VNPay callback signature validation failed.');

            return $this->paymentRedirect(false);
        }

        $transactionReference = $inputData['vnp_TxnRef'] ?? null;
        $transactionNumber = $inputData['vnp_TransactionNo'] ?? null;
        $isSuccessful = ($inputData['vnp_ResponseCode'] ?? null) === '00'
            && ($inputData['vnp_TransactionStatus'] ?? '00') === '00';

        if (!$transactionReference || !$isSuccessful) {
            return $this->paymentRedirect(false);
        }

        $payment = DB::transaction(function () use ($transactionReference, $transactionNumber, $inputData) {
            $payment = Payment::where('payment_code', $transactionReference)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                return null;
            }

            $expectedAmount = (int) round(((float) $payment->total_amount) * 100);
            $receivedAmount = filter_var($inputData['vnp_Amount'] ?? null, FILTER_VALIDATE_INT);

            if ($receivedAmount === false || $receivedAmount !== $expectedAmount) {
                Log::warning('VNPay callback amount does not match the invoice.', [
                    'payment_code' => $payment->payment_code,
                    'expected_amount' => $expectedAmount,
                    'received_amount' => $inputData['vnp_Amount'] ?? null,
                ]);

                return null;
            }

            if ($payment->payment_status === PaymentStatus::Paid) {
                return $payment;
            }

            if ($payment->payment_status !== PaymentStatus::Unpaid) {
                return null;
            }

            $payment->update([
                'payment_status' => 'paid',
                'vnp_transaction_no' => $transactionNumber,
                'payment_date' => now(),
            ]);

            $payment->setAttribute('was_just_paid', true);

            return $payment;
        });

        if (!$payment) {
            return $this->paymentRedirect(false);
        }

        if ($payment->getAttribute('was_just_paid')) {
            $user = optional($payment->student)->parentUser;

            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new Paymentsuccessful($user, $payment));
                } catch (\Throwable $exception) {
                    Log::error('Payment succeeded but its confirmation email failed.', [
                        'payment_code' => $payment->payment_code,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }

        return $this->paymentRedirect(true);
    }

    private function paymentRedirect(bool $successful)
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        return redirect($frontendUrl . ($successful ? '/payment-success' : '/payment-failed'));
    }
}
