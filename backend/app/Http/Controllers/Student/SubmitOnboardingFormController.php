<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubmitOnboardingFormController extends Controller
{
    public function submitOnboardingForm(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        // 🧩 Validate dữ liệu từ frontend
        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:255',
            'gender'        => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date_format:d/m/Y',
            'student_code'  => 'required|string|max:50|unique:students,student_code',
            'phone'         => 'nullable|regex:/^[0-9]{9,11}$/',
            'start_date'    => 'required|date_format:d/m/Y',
            'end_date'      => 'required|date_format:d/m/Y|after:start_date',
            'room_id'       => 'required|exists:rooms,id',
        ], [
            'room_id.required' => 'Bạn phải chọn phòng.',
            'room_id.exists'   => 'Phòng không tồn tại.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 🔎 Kiểm tra phòng và số chỗ trống
            $room = Room::findOrFail($request->room_id);

            $occupied = $room->students()->count(); // giả sử Room có relation students()
            $available = $room->capacity - $occupied;

            if ($available <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phòng đã đầy. Vui lòng chọn phòng khác.',
                ], 409);
            }

            // 🔎 Tạo sinh viên
            $student = Student::create([
                'user_id'       => $user->id,
                'full_name'     => $request->full_name,
                'student_code'  => $request->student_code,
                'phone'         => $request->phone,
                'gender'        => $request->gender,
                'date_of_birth' => $request->date_of_birth
                    ? Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d')
                    : null,
                'status'        => 'Active',
                'room_id'       => $request->room_id, // gán phòng
            ]);

            // 🔎 Tạo hợp đồng mới với trạng thái Approved
            $student->contracts()->create([
                'start_date'     => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d'),
                'end_date'       => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d'),
                'deposit_amount' => 200000,
                'status'         => 'Approved',
            ]);

            Payment::create([
                'student_id'     => $student->id,
                'room_id'        => $room->id,
                'payment_code'   => 'DEP-' . strtoupper(uniqid()), // Mã hoá đơn
                'total_amount'   => 200000, // 💰 cùng số tiền cọc
                'payment_status' => 'unpaid',
                'description'    => 'Tiền cọc ký túc xá',
                'month'          => 'Tháng 11',
                'year'           => Carbon::now()->format('Y'),
            ]);


            DB::commit();

            $student->load(['user', 'contracts', 'room']); // load quan hệ để trả về

            return response()->json([
                'status'  => true,
                'message' => 'Nộp đơn thành công! Hợp đồng được phê duyệt ngay.',
                'data'    => $student,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'        => false,
                'message'       => 'Đã có lỗi xảy ra. Vui lòng thử lại.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }
}
