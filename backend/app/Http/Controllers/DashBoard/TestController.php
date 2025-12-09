<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Room;
use App\Models\UtilityPrice;
use App\Service\Payment\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TestController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'room_id' => 'required|exists:rooms,id',
                'electricity_usage' => 'required|numeric|min:0',
                'water_usage' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000',
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
            DB::transaction(function () use ($request) {
                $room = Room::with('students')->findOrFail($request->room_id);
                $students = $room->students;

                if ($students->isEmpty()) {
                    throw new \Exception('Phòng này không có sinh viên.');
                }

                $exists = Payment::where('room_id', $room->id)
                    ->where('month', $request->month)
                    ->where('year', $request->year)
                    ->exists();

                if ($exists) {
                    throw new \Exception("Phòng {$room->room_code} đã có hóa đơn cho tháng {$request->month}/{$request->year}.");
                }

                $utility = UtilityPrice::first();
                if (!$utility) {
                    throw new \Exception('Chưa cấu hình giá điện nước.');
                }

                $electricityRate = $utility->electricity_price; // ✅ đúng tên cột
                $waterRate = $utility->water_price;
                $baseFee = $room->price;

                $electricity = $request->input('electricity_usage', 0);
                $water = $request->input('water_usage', 0);
                $description = $request->input('description', 'Thanh toán ký túc xá');

                $totalStudents = $students->count();

                $sharedElectric = $electricity;
                $sharedWater = $water;

                foreach ($students as $student) {
                    $electricityCost = $sharedElectric * $electricityRate;
                    $waterCost = $sharedWater * $waterRate;
                    $total = $baseFee + ($electricityCost + $waterCost) / $totalStudents;

                    Payment::create([
                        'student_id' => $student->id,
                        'room_id' => $room->id,
                        'payment_code' => 'PAY-' . strtoupper(Str::random(8)),
                        'electricity_usage' => $sharedElectric,
                        'water_usage' => $sharedWater,
                        'total_amount' => ceil($total),
                        'description' => $description,
                        'payment_status' => 'unpaid',
                        'month' => $request->month,
                        'year' => $request->year,
                    ]);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Tạo hóa đơn thanh toán thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo thanh toán: ' . $e->getMessage(),
            ], 500);
        }
    }
}
