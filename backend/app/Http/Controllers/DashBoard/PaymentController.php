<?php

namespace App\Http\Controllers\DashBoard;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\PaymentCreated;
use App\Models\ParentStudent;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Models\UtilityPrice;
use App\Service\Payment\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $month = $request->input('month', '');
        $year = $request->input('year', '');
        $status = $request->input('status', '');
        $perPage = 8;

        $query = Payment::with(['student', 'room']);

        if (!empty($search)) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            })->orWhereHas('room', function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%");
            });
        }

        // 🧠 Áp dụng filter Month
        if (!empty($month) && $month !== 'all') {
            $query->where('month', $month);
        }

        // 🧠 Áp dụng filter Year
        if (!empty($year) && $year !== 'all') {
            $query->where('year', $year);
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('payment_status', $status);
        }




        $payment = $query->orderBy('month', 'desc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách thanh toán thành công',
            'data' => $payment->items(),
            'pagination' => [
                'total' => $payment->total(),
                'per_page' => $payment->perPage(),
                'current_page' => $payment->currentPage(),
                'last_page' => $payment->lastPage(),
            ],
        ]);
    }


    public function store(Request $request)
    {
        // ✅ Validate đầu vào
        $validator = Validator::make(
            $request->all(),
            [
                'room_id' => 'required|exists:rooms,id',
                'electricity_usage' => 'required|numeric|min:0',
                'water_usage' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // ✅ Lấy thông tin phòng theo room_code
            $room = Room::with('students')->findOrFail($request->room_id);
            $students = $room->students;

            if ($students->isEmpty()) {
                throw new \Exception("Phòng {$room->room_code} không có sinh viên.");
            }

            // ✅ Lấy tháng và năm hiện tại
            $now = now();
            $month = $now->month;
            $year = $now->year;

            // ✅ Kiểm tra trùng hóa đơn
            $exists = Payment::where('room_id', $room->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($exists) {
                throw new \Exception("Phòng {$room->room_code} đã có hóa đơn cho tháng {$month}/{$year}.");
            }

            // ✅ Lấy giá điện nước
            $utility = UtilityPrice::first();
            if (!$utility) {
                throw new \Exception('Chưa cấu hình giá điện nước.');
            }

            $electricityRate = $utility->electricity_price;
            $waterRate = $utility->water_price;
            $baseFee = $room->price;

            $electricity = $request->input('electricity_usage', 0);
            $water = $request->input('water_usage', 0);
            $description = $request->input('description', "Thanh toán KTX tháng {$month}/{$year}");
            $totalStudents = $students->count();

            // ✅ Chia đều điện nước cho sinh viên
            $sharedElectric = $electricity;
            $sharedWater = $water;

            // ✅ Hạn thanh toán = hôm nay + 7 ngày
            $paymentDate = now()->addDays(7);

            $createdPayments = [];

            foreach ($students as $student) {
                $electricityCost = $sharedElectric * $electricityRate;
                $waterCost = $sharedWater * $waterRate;
                $total = $baseFee + ($electricityCost + $waterCost) / $totalStudents;

                $payment = Payment::create([
                    'student_id' => $student->id,
                    'room_id' => $room->id,
                    'payment_code' => 'PAY-' . strtoupper(Str::random(8)),
                    'electricity_usage' => $sharedElectric,
                    'water_usage' => $sharedWater,
                    'total_amount' => ceil($total),
                    'description' => $description,
                    'payment_status' => 'unpaid',
                    'month' => $month,
                    'year' => $year,
                    'payment_date' => $paymentDate, // ✅ tự động +7 ngày
                ]);

                $createdPayments[] = $payment;
            }

            DB::commit();

            // ✅ Sau khi tạo xong hóa đơn, gửi mail cho tất cả phụ huynh
            foreach ($students as $student) {
                $parentLink = ParentStudent::where('student_id', $student->id)->first();

                if ($parentLink) {
                    $parentUser = User::find($parentLink->user_id);

                    if ($parentUser && $parentUser->email) {
                        try {
                            // Tìm hóa đơn tương ứng của sinh viên này
                            $studentPayment = collect($createdPayments)
                                ->firstWhere('student_id', $student->id);

                            Mail::to($parentUser->email)->send(new PaymentCreated($parentUser, $student, $studentPayment));
                        } catch (\Exception $mailEx) {
                            Log::error('❌ Gửi mail thất bại cho phụ huynh ID: ' . $parentUser->id . ' | Lỗi: ' . $mailEx->getMessage());
                        }
                    } else {
                        Log::warning('⚠️ Không tìm thấy email phụ huynh cho student_id: ' . $student->id);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Tạo hóa đơn và gửi mail cho phụ huynh thành công!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo thanh toán: ' . $e->getMessage(),
            ], 500);
        }
    }




    /**
     * Hiển thị chi tiết 1 thanh toán
     */
    public function show(string $id)
    {
        $payment = Payment::with(['student', 'room'])->find($id);

        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thanh toán',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin thanh toán thành công',
            'data' => $payment,
        ], 200);
    }

    /**
     * Cập nhật trạng thái thanh toán (VD: từ "unpaid" → "paid")
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => ['required', Rule::enum(PaymentStatus::class)],
        ], [
            'payment_status.required' => 'Trạng thái thanh toán không được để trống',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thanh toán',
            ], 404);
        }

        $payment->payment_status = $request->payment_status;
        $payment->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái thanh toán thành công',
            'data' => $payment,
        ]);
    }

    /**
     * Xóa 1 hóa đơn thanh toán
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn thanh toán',
            ], 404);
        }

        $payment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa hóa đơn thanh toán thành công',
        ]);
    }
}
