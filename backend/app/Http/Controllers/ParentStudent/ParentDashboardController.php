<?php

namespace App\Http\Controllers\ParentStudent;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmRefund;
use Illuminate\Http\Request;
use App\Models\ParentStudent;
use App\Models\Student;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ParentDashboardController extends Controller
{
    /**
     * ✅ Hiển thị dashboard phụ huynh (thông tin con và hóa đơn)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Tìm thông tin phụ huynh
        $parent = ParentStudent::where('user_id', $user->id)->first();

        if (!$parent) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin phụ huynh trong hệ thống'
            ], 404);
        }

        // Kiểm tra liên kết học sinh
        if (!$parent->student_id) {
            return response()->json([
                'status' => false,
                'message' => 'Phụ huynh chưa được liên kết với học sinh nào'
            ], 404);
        }

        // Lấy thông tin học sinh + phòng + hóa đơn
        $student = Student::with(['room', 'payments'])->find($parent->student_id);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy học sinh liên kết với phụ huynh này'
            ], 404);
        }

        // ✅ Xử lý danh sách hóa đơn (thêm ID để phụ huynh có thể thanh toán)
        $payments = $student->payments->map(function ($payment) {
            return [
                'id'           => $payment->payment_id, // ⭐ Quan trọng để thanh toán
                'payment_code' => $payment->payment_code,
                'month'        => $payment->month,
                'year'         => $payment->year,
                'electricity_usage' => $payment->electricity_usage,
                'water_usage' => $payment->water_usage,
                'description'  => $payment->description,
                'total_amount' => $payment->total_amount, // Số tiền gốc
                'total_amount_formatted' => number_format($payment->total_amount, 0, ',', '.') . 'đ',
                'status'       => $payment->payment_status->value,
                'created_at'   => $payment->created_at->format('d/m/Y H:i'),
                // ⭐ Cho phép thanh toán nếu chưa thanh toán
                'can_pay'      => $payment->payment_status === PaymentStatus::Unpaid,
            ];
        });

        return response()->json([
            'status' => true,
            'parent' => [
                'name'    => $parent->full_name ?? $user->name,
                'email'   => $user->email,
                'phone'   => $parent->phone,
                'address' => $parent->address,
            ],
            'student' => [
                'name'         => $student->full_name,
                'student_code' => $student->student_code,
                'phone'        => $student->phone,
                'status'       => $student->status,
                'avatar'       => $student->avatar ?? null,
            ],
            'room' => [
                'name'     => $student->room->room_code ?? 'Chưa có phòng',
                'building' => $student->room->area ?? 'Không xác định',
                'capacity' => $student->room->capacity ?? 0,
                'price'    => $student->room->price ?? 0,
            ],
            'payments' => $payments,
        ]);
    }


    public function receiveRefund(Request $request, $payment_id)
    {
        $user = Auth::user();
        $parent = ParentStudent::where('user_id', $user->id)->first();

        if (!$parent) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin phụ huynh'
            ], 404);
        }

        $student = Student::find($parent->student_id);
        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin học sinh'
            ], 404);
        }

        $payment = Payment::where('payment_id', $payment_id)
            ->where('student_id', $student->id)
            ->first();

        if (!$payment || $payment->payment_status !== PaymentStatus::RefundPending) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn đang hoàn tiền hợp lệ'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::where('payment_id', $payment_id)
                ->where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if (!$payment || $payment->payment_status !== PaymentStatus::RefundPending) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Phiếu hoàn tiền đã được xử lý hoặc không còn hợp lệ.',
                ], 409);
            }

            $payment->update([
                'payment_status' => 'refunded',
                'payment_date' => now(),
            ]);

            DB::commit();

            $parentUser = User::find($parent->user_id);
            if ($parentUser && $parentUser->email) {
                try {
                    Mail::to($parentUser->email)->send(new ConfirmRefund($parentUser, $payment));
                } catch (\Throwable $mailEx) {
                    Log::error('Gửi mail xác nhận hoàn tiền thất bại.', [
                        'payment_id' => $payment->payment_id,
                        'error' => $mailEx->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Đã xác nhận hoàn tiền. Lịch sử sinh viên, hợp đồng và hóa đơn được giữ lại.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xử lý hoàn trả: ' . $e->getMessage()
            ], 500);
        }
    }
}
