<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\ParentStudent;
use App\Models\Student;
use App\Models\User;
use App\Service\User\UserServiceInterface;
use App\Utilities\Constant;
use App\Utilities\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    //
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"     => "required|string|max:225",
            "email"    => "required|email|unique:users",
            "password" => "required|min:6|confirmed",
            "role"     => "required|in:Student,Parent,Admin",
        ], [
            "name.required"       => "Tên không được để trống",
            "email.required"      => "Email không được để trống",
            "email.email"         => "Email không hợp lệ",
            "email.unique"        => "Email đã tồn tại",
            "password.required"   => "Mật khẩu không được để trống",
            "password.min"        => "Mật khẩu phải có ít nhất 6 ký tự",
            "password.confirmed"  => "Mật khẩu xác nhận không khớp",
            "role.required"       => "Role không được để trống",
            "role.in"             => "Role không hợp lệ (Student, Parent, Admin)",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Validate thêm theo role
        if ($data["role"] === "Student") {
            $validator = Validator::make($data, [
                "student_code" => "required|unique:students",
                "full_name"    => "required|string|max:255",
            ], [
                "student_code.required" => "Mã sinh viên không được để trống",
                "student_code.unique"   => "Mã sinh viên đã tồn tại",
                "full_name.required"    => "Họ tên không được để trống",
            ]);
        } elseif ($data["role"] === "Parent") {
            $validator = Validator::make($data, [
                "student_code" => "required|exists:students,student_code",
                "full_name"    => "required|string|max:255",
            ], [
                "student_code.required" => "Cần nhập mã sinh viên để liên kết",
                "student_code.exists"   => "Sinh viên không tồn tại",
                "full_name.required"    => "Họ tên phụ huynh không được để trống",
            ]);
        }

        if (isset($validator) && $validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Tạo user
        $user = User::create([
            "name"     => $data["name"],
            "email"    => $data["email"],
            "password" => Hash::make($data["password"]),
            "role"     => $data["role"],
            "status"   => "Active",
        ]);

        // Tạo profile tùy role
        if ($data["role"] === "Student") {
            Student::create([
                "user_id"      => $user->id,
                "student_code" => $data["student_code"],
                "full_name"    => $data["full_name"],
                "gender"       => $data["gender"] ?? null,
                "date_of_birth" => $data["date_of_birth"] ?? null,
                "status"       => "Active",
            ]);
        } elseif ($data["role"] === "Parent") {
            // Lấy student_id từ student_code
            $student = Student::where("student_code", $data["student_code"])->first();

            ParentStudent::create([
                "user_id"    => $user->id,
                "student_id" => $student->id, // dùng id chứ không dùng code
                "full_name"  => $data["full_name"],
                "phone"      => $data["phone"] ?? null,
                "address"    => $data["address"] ?? null,
            ]);
        }

        return response()->json([
            "status"  => true,
            "message" => "Đăng ký thành công",
            "data"    => $user
        ], 201);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác',
            ], 401);
        }
        if ($user->status !== 'Active') {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản hiện đang không hoạt động',
            ], 403); // 403 Forbidden
        }

        // Tạo token riêng cho từng role + device nếu muốn
        $tokenName = $user->role . '_' . time();
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login thành công',
            'token'  => $token,
            'user'   => $user
        ]);
    }
}
