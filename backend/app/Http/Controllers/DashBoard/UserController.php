<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student; // <-- Thêm
use App\Models\ParentStudent; // <-- Thêm
use App\Models\DormManager; // <-- Thêm
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Thêm
use Illuminate\Support\Facades\Hash; // <-- Thêm
use Illuminate\Support\Facades\Validator; // <-- Thêm

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = 20;

        // Giả sử relationship là số ít (1 user có 1 profile)
        // Sửa: 'parent_students' -> 'parent_student', 'dorm_managers' -> 'dorm_manager'
        $query = User::with('student', 'parent_student', 'dorm_manager');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {

                $q->whereHas('student', function ($subQ) use ($search) {
                    $subQ->where('full_name', 'like', "%{$search}%");
                })
                    // Sửa lỗi gõ chữ: 'parent_studnets' -> 'parent_student'
                    ->orWhereHas('parent_student', function ($subQ) use ($search) {
                        $subQ->where('full_name', 'like', "%{$search}%");
                    })
                    // Sửa: 'dorm_managers' -> 'dorm_manager'
                    ->orWhereHas('dorm_manager', function ($subQ) use ($search) {
                        $subQ->where('full_name', 'like', "%{$search}%");
                    });

                // Cũng tìm theo email hoặc tên trong bảng user
                $q->orWhere('name', 'like', "%{$search}%");
                $q->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'asc')->paginate($perPage);

        return response()->json([
            "status" => true,
            "message" => 'Lấy danh sách người dùng thành công',
            "data" => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Dùng cho API, thường không cần
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"     => "required|string|max:225",
            "email"    => "required|email|unique:users",
            "password" => "required|min:6|confirmed",
            "role"     => "required|in:Admin", // Chỉ cho phép Admin
        ], [
            "name.required"       => "Tên không được để trống",
            "email.required"      => "Email không được để trống",
            "email.email"         => "Email không hợp lệ",
            "email.unique"        => "Email đã tồn tại",
            "password.required"   => "Mật khẩu không được để trống",
            "password.min"        => "Mật khẩu phải có ít nhất 6 ký tự",
            "password.confirmed"  => "Mật khẩu xác nhận không khớp",
            "role.required"       => "Role không được để trống",
            "role.in"             => "Chỉ được tạo Admin",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'email', 'password', 'role']);

        // Tạo user chỉ với role Admin
        $user = User::create([
            "name"     => $data["name"],
            "email"    => $data["email"],
            "password" => Hash::make($data["password"]),
            "role"     => "Admin",
            "status"   => "Active",
        ]);

        return response()->json([
            "status"  => true,
            "message" => "Tạo Admin thành công",
            "data"    => $user
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('student', 'parent_student', 'dorm_manager')->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin người dùng thành công',
            'data' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Dùng cho API, thường không cần
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy người dùng'], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255', // full_name
                // Sửa validation: unique phải ignore chính user này
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:6|confirmed', // Nullable: chỉ cập nhật nếu có
                'status' => 'nullable|in:active,inactive',
                // Thường không cho phép đổi Role ở đây, vì logic rất phức tạp
            ],
            [
                'name.required' => 'Họ và tên không được để trống',
                'email.required' => 'Email không được để trống',
                'email.email' => 'Email không hợp lệ',
                'email.unique' => 'Email đã tồn tại',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Cập nhật User
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status ?? $user->status,
            ];

            // Chỉ cập nhật mật khẩu nếu được cung cấp
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // 2. Cập nhật `full_name` trong profile liên quan
            switch ($user->role) {
                case 'student':
                    if ($user->student) {
                        $user->student->update(['full_name' => $request->name]);
                    }
                    break;
                case 'parent':
                    if ($user->parent_student) {
                        $user->parent_student->update(['full_name' => $request->name]);
                    }
                    break;
                case 'staff':
                    if ($user->dorm_manager) {
                        $user->dorm_manager->update(['full_name' => $request->name]);
                    }
                    break;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật người dùng thành công',
                'data' => $user->load('student', 'parent_student', 'dorm_manager') // Trả về data mới
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy người dùng'], 404);
            }

            // Xóa profile liên quan TRƯỚC
            // (Nếu bạn đã setup 'onDelete('cascade')' trong migration thì không cần bước này)
            switch ($user->role) {
                case 'student':
                    if ($user->student) $user->student->delete();
                    break;
                case 'parent':
                    if ($user->parent_student) $user->parent_student->delete();
                    break;
                case 'staff':
                    if ($user->dorm_manager) $user->dorm_manager->delete();
                    break;
            }

            // Xóa User
            $user->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Xóa người dùng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Xóa thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}
