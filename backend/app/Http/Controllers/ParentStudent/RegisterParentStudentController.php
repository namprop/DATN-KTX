<?php

namespace App\Http\Controllers\ParentStudent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\ParentStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeMail;

class RegisterParentStudentController extends Controller
{
    public function registerParentStudent(Request $request)
    {
        // ✅ Validate dữ liệu đầu vào
        $validator = Validator::make(
            $request->all(),
            [
                "name"        => "required|string|max:225",
                "email"       => "required|email|unique:users,email",
                "password"    => "required|min:6|confirmed",
                "role"        => "required|in:Student,Parent,Admin",
                "student_id"  => "required|string", // Mã sinh viên (student_code)
                "gender"      => "nullable|in:Male,Female,Other",
                "phone"       => "nullable|string|max:20",
                "address"     => "nullable|string|max:255",
            ],
            [
                "name.required"       => "Tên không được để trống",
                "email.required"      => "Email không được để trống",
                "email.email"         => "Email không hợp lệ",
                "email.unique"        => "Email đã tồn tại",
                "password.required"   => "Mật khẩu không được để trống",
                "password.min"        => "Mật khẩu phải có ít nhất 6 ký tự",
                "password.confirmed"  => "Mật khẩu xác nhận không khớp",
                "role.required"       => "Role không được để trống",
                "role.in"             => "Role không hợp lệ (Student, Parent, Admin)",
                "student_id.required" => "Mã sinh viên không được để trống",
            ]
        );

        // ❌ Nếu dữ liệu không hợp lệ
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // ✅ Kiểm tra mã sinh viên có tồn tại không
        $student = Student::where('student_code', $request->student_id)->first();

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sinh viên có mã: ' . $request->student_id,
            ], 404);
        }

        // ✅ Tạo user cho phụ huynh
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'Parent', // Mặc định là phụ huynh
            'status'   => 'Active',
        ]);

        // ✅ Tạo bản ghi trong bảng parent_students
        $parent = ParentStudent::create([
            'user_id'    => $user->id,
            'student_id' => $student->id,
            'full_name'  => $request->name, // 🔁 Dùng name làm full_name
            'gender'     => $request->gender,
            'phone'      => $request->phone,
            'address'    => $request->address,
        ]);

        Mail::to($user->email)->send(new WelcomeMail($user));

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký phụ huynh thành công',
            'data'    => [
                'user'   => $user,
                'parent' => $parent,
            ]
        ], 201);
    }
}
