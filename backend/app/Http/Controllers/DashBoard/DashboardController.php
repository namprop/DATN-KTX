<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Facilities;
use App\Models\Payment;
use App\Models\Room;
use App\Models\SchoolStudent;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // Tổng học sinh đang hoạt động

        $student = Student::where('status', 'Active')->count();
        $StudentInactived = Student::where('status', 'Inactive')->count();
        $schoolStudent = SchoolStudent::count();

        // $totalStudents = Student::where('status', 'Active')->count();

        // Tổng nhân viên (bao gồm Admin + Staff)
        $totalStaff = User::whereIn('role', ['Admin', 'Staff'])->count();

        // Phòng đang sử dụng
        $usedRooms = Room::whereHas('students')->count();
        $totalRooms = Room::count();
        $roomUsageRate = $totalRooms > 0 ? round(($usedRooms / $totalRooms) * 100, 1) : 0;

        // Hợp đồng đang hoạt động
        $activeContracts = Contract::where('status', 'Active')->count();

        // Thanh toán tháng này
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $paidThisMonth = Payment::where('payment_status', 'paid')
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->count();

        // Chờ thanh toán
        $pendingPayments = Payment::where('payment_status', 'unpaid')->count();

        // Thiết bị hỏng
        $brokenFacilities = Facilities::where('status', 'broken')->count();

        // Trả về JSON cho frontend (Vue)
        return response()->json([
            'total_students' => $student,
            'total_students_inactive' => $StudentInactived,
            'total_schoolstudents' => $schoolStudent ,
            'total_staff' => $totalStaff,
            'used_rooms' => $usedRooms,
            'total_rooms' => $totalRooms,
            'room_usage_rate' => $roomUsageRate,
            'active_contracts' => $activeContracts,
            'paid_this_month' => $paidThisMonth,
            'pending_payments' => $pendingPayments,
            'broken_facilities' => $brokenFacilities,
        ]);
    }
}
