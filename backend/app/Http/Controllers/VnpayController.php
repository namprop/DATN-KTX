<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Paymentsuccessful;

class VnpayController extends Controller
{
    public function createPayment(Request $request)
    {
        $vnp_TmnCode    = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url        = config('vnpay.vnp_Url');
        $vnp_ReturnUrl  = config('vnpay.vnp_ReturnUrl');

        $payment = Payment::find($request->input('payment_id'));
        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
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

    public function vnpReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

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

        if ($secureHash === $vnp_SecureHash) {
            $payment = Payment::where('payment_code', $inputData['vnp_TxnRef'])->first();

            if ($payment) {

                // >>>>>> LOGIC TÌM PHỤ HUYNH (USER) <<<<<<
                // Lấy đối tượng User (Phụ huynh) qua Student và ParentStudent
                $user = optional($payment->student)->parentUser;
                // ----------------------------------------

                if ($inputData['vnp_ResponseCode'] == '00') {
                    // thanh toán thành công
                    $payment->update([
                        'payment_status' => 'paid',
                        'vnp_transaction_no' => $inputData['vnp_TransactionNo'] ?? null,
                        'payment_date' => now(),
                    ]);

                    // >>>>>> LOGIC GỬI EMAIL <<<<<<
                    if ($user && $user->email) {
                        // Cần đảm bảo các use statement (Mail, Paymentsuccessful) đã có ở đầu file
                        Mail::to($user->email)->send(new Paymentsuccessful($user, $payment));
                    } else {
                        // Log lại lỗi nếu không gửi được email (không tìm thấy phụ huynh)
                        \Illuminate\Support\Facades\Log::warning('Payment successful, but Parent User/Email not found for payment code: ' . $payment->payment_code);
                    }
                    // -----------------------------

                    return redirect("http://localhost:4000/payment-success");
                } else {
                    // thất bại
                    $payment->update(['payment_status' => 'unpaid']);
                    return redirect("http://localhost:4000/payment-failed");
                }
            } else {
                return redirect("http://localhost:4000/payment-failed");
            }
        } else {
            return redirect("http://localhost:4000/payment-failed");
        }
    }
}
