<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Contract;

class CheckStatusStudentController extends Controller
{
    public function checkStatusStudent(Request $request)
    {
        $user = Auth::user();

        // 🔸 Kiểm tra user có tồn tại hay không
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Người dùng chưa đăng nhập.'
            ], 401);
        }

        // 🔸 Lấy thông tin sinh viên dựa trên user_id
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin sinh viên.'
            ], 404);
        }

        // 🔸 Lấy hợp đồng mới nhất của sinh viên (nếu có)
        $contract = Contract::where('student_id', $student->id)
            ->latest('created_at')
            ->first();

        if (!$contract) {
            return response()->json([
                'status' => true,
                'contract_status' => 'None',
                'message' => 'Sinh viên chưa có hợp đồng.'
            ], 200);
        }

        // 🔸 Trả về trạng thái hợp đồng
        return response()->json([
            'status' => true,
            'contract_status' => $contract->status, // Pending | Approved | Rejected | ...
            'contract_id' => $contract->id,
            'start_date' => $contract->start_date,
            'end_date' => $contract->end_date,
        ], 200);
    }
}
