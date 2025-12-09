<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\SchoolStudent;
use App\Models\Student;
use App\Service\Room\RoomServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentVerifyController extends Controller
{

    protected $roomService;

    public function __construct(RoomServiceInterface $roomService)
    {
        $this->roomService = $roomService;
    }


    public function verifyStudent(Request $request)
    {
        // 1. Xác thực Mã sinh viên tồn tại trong danh sách trường
        $schoolStudent = DB::table('school_students')
            ->where('student_code', $request->student_code)
            ->first();

        if (!$schoolStudent) {
            return response()->json([
                'status' => false,
                'message' => 'Mã sinh viên chưa được xác thực (Không tồn tại trong danh sách sinh viên).',
            ]);
        }

        // 2. 🌟 Bổ sung: Kiểm tra xem Mã sinh viên đã đăng ký KTX chưa
        $ktxStudent = Student::where('student_code', $request->student_code)->first();

        if ($ktxStudent) {
            return response()->json([
                'status' => false,
                'message' => 'Mã sinh viên này đã được sử dụng để đăng ký KTX.',
            ]);
        }

        // 3. Xác thực thành công
        return response()->json([
            'status' => true,
            'message' => 'Mã sinh viên hợp lệ và chưa đăng ký KTX.',
            'data' => $schoolStudent,
        ]);
    }

    public function displayRoom(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');      // ✅ Nhận riêng
        $description = $request->query('description', ''); // ✅ Nhận riêng
        $perPage = 20;

        $query = Room::withCount('students');

        // 🔍 Nếu có từ khóa tìm kiếm
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🧠 Áp dụng filter status
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // 🧠 Áp dụng filter description
        if (!empty($description) && $description !== 'all') {
            $query->where('description', $description);
        }

        $rooms = $query
            ->orderBy('room_code', 'asc')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            "status" => true,
            "message" => "Lấy danh sách phòng thành công",
            "data" => $rooms->items(),
            "pagination" => [
                'total' => $rooms->total(),
                'per_page' => $rooms->perPage(),
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
            ],
        ]);
    }
}
