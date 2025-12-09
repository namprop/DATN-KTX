<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterStudentsController extends Controller
{
       public function registerStudent(Request $request)
    {
        $validate = Validator(
            $request->all(),
            [
                "name" => "required|string|max:225",
                "email" => "required|email|unique:users",
                "password" => "required|min:6|confirmed",
                "role" => "required|in:Student,Parent,Admin",
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

            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validate->errors()
            ], 422);
        }

        $data = $request->all();

        $user = User::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => Hash::make($data["password"]),
            "role" => "Student",
            "status" => "Active",
        ]);

        return response()->json([
            "status" => true,
            "message" => "Đăng ký thành công",
            "data" => $user
        ], 201);
    }
}
