<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Service\Room\RoomServiceInterface;
use App\Service\Student\StudentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $studentService;
    protected $roomService;

    public function __construct(
        StudentServiceInterface $studentService,
        RoomServiceInterface $roomService
    ) {
        $this->studentService = $studentService;
        $this->roomService = $roomService;
    }


    public function index(Request $request)
    {
        $keyword = $request->query('search'); // lấy param ?search=...
        $columns = ['full_name', 'student_code']; // các cột được tìm kiếm

        $students = $this->studentService->getAllStudentsWithUser($columns, $keyword, 20);

        // 🔹 Cập nhật trạng thái Active nếu đã thanh toán hết
        foreach ($students as $student) {
            if (!in_array($student->status, ['Inactive'])) {
                $unpaidCount = $student->payments()
                    ->where('payment_status', '!=', 'paid')
                    ->count();

                if ($unpaidCount == 0 && $student->status != 'Active') {
                    $student->status = 'Active';
                    $student->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách học sinh thành công',
            'data' => $students->items(),
            'pagination' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make(
            $request->all(),
            [
                "name"          => "required|string|max:225",
                "full_name"     => "required|string|max:225",
                "email"         => "required|email|unique:users",
                "password"      => "required|min:6|confirmed",
                "student_code"  => "required|unique:students",
                "gender"        => "nullable|in:Male,Female,Other",
                "phone"         => "nullable|regex:/^[0-9]{9,11}$/",
                "date_of_birth" => "nullable|date",
                "avatar"        => "nullable|image|mimes:jpg,jpeg,png|max:2048",
                'room_code' => 'nullable|exists:rooms,room_code',


            ],
            [
                "name.required"          => "Tên không được để trống",
                "full_name.required"     => "Họ tên không được để trống",
                "email.required"         => "Email không được để trống",
                "email.email"            => "Email không hợp lệ",
                "email.unique"           => "Email đã tồn tại",
                "password.required"      => "Mật khẩu không được để trống",
                "password.min"           => "Mật khẩu phải có ít nhất 6 ký tự",
                "password.confirmed"     => "Mật khẩu xác nhận không khớp",
                "student_code.required"  => "Mã học sinh không được để trống",
                "student_code.unique"    => "Mã học sinh đã tồn tại",
                "phone.regex"            => "Số điện thoại chỉ gồm 9-11 chữ số",
                "gender.in"              => "Giới tính chỉ nhận Male, Female hoặc Other",
                "date_of_birth.date"     => "Ngày sinh phải đúng định dạng",
                "avatar.image"           => "File avatar phải là ảnh",
                "avatar.mimes"           => "Ảnh avatar chỉ hỗ trợ jpg, jpeg, png",
                "avatar.max"             => "Ảnh avatar không vượt quá 2MB",
                'room_code.exists'       => 'Mã phòng không tồn tại',


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
            // chỉ lấy dữ liệu đã qua validate
            $data = $validator->validated();

            // Transaction: nếu lỗi rollback toàn bộ
            DB::beginTransaction();

            $student = $this->studentService->createStudent($data, $request);

            DB::commit();

            return response()->json([
                "status"  => true,
                "message" => 'Tạo học sinh thành công',
                "data"    => $student
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"  => false,
                "message" => "Có lỗi xảy ra khi tạo học sinh",
                "error"   => $e->getMessage()
            ], 422);
        }
    }





    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $student = $this->studentService->findStudentWithUser($id);



        if (!$student) {
            return response()->json([
                "status"  => false,
                "message" => 'Học sinh không tồn tại',
                "data"    => null
            ], 404);
        }

        return response()->json([
            "status"  => true,
            "message" => 'Lấy thông tin học sinh thành công',
            "data"    => $student
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $sutdent = $this->studentService->find($id);
        if (!$sutdent) {
            return response()->json(
                [
                    "status" => false,
                    "message" => 'Học sinh không tồn tại',
                    "data" => null
                ],
                404
            );
        }
        return response()->json(
            [
                "status" => true,
                "message" => 'Lấy thông tin học sinh thành công',
                "data" => $sutdent
            ],
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $student = $this->studentService->find($id);

        if (!$student) {
            return response()->json(
                [
                    "status" => false,
                    "message" => 'Học sinh không tồn tại',
                    "data" => null
                ],
                404
            );
        }

        $validator = Validator::make(
            $request->all(),
            [
                "name"          => "required|string|max:225",
                "full_name"     => "required|string|max:225",
                "email"         => "required|email|unique:users,email," . $student->user_id . ",id",
                "student_code"  => "required|unique:students,student_code," . $id . ",id",
                "phone"         => "nullable|regex:/^[0-9]{9,11}$/",
                "gender"        => "nullable|in:Male,Female,Other",
                "date_of_birth" => "nullable|date",
                "avatar"        => "nullable|image|mimes:jpg,jpeg,png|max:2048",
                'room_code'     => 'nullable|exists:rooms,room_code',
            ],
            [
                "name.required"          => "Tên không được để trống",
                "full_name.required"     => "Họ tên không được để trống",
                "email.required"         => "Email không được để trống",
                "email.email"            => "Email không hợp lệ",
                "email.unique"           => "Email đã tồn tại",
                "student_code.required"  => "Mã học sinh không được để trống",
                "student_code.unique"    => "Mã học sinh đã tồn tại",
                "gender.in"              => "Giới tính chỉ nhận Male, Female hoặc Other",
                "date_of_birth.date"     => "Ngày sinh phải đúng định dạng",
                "phone.regex"            => "Số điện thoại chỉ gồm 9–11 chữ số",
                "avatar.image"           => "File avatar phải là ảnh",
                "avatar.mimes"           => "Ảnh avatar chỉ hỗ trợ jpg, jpeg, png",
                "avatar.max"             => "Ảnh avatar không vượt quá 2MB",
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
            // chỉ lấy dữ liệu đã qua validate
            $data = $validator->validated();

            // Transaction: nếu lỗi rollback toàn bộ
            DB::beginTransaction();

            $student = $this->studentService->updateStudent($id, $data, $request);

            DB::commit();

            return response()->json([
                "status"  => true,
                "message" => 'Cập nhật học sinh thành công',
                "data"    => $student
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"  => false,
                "message" => "Có lỗi xảy ra khi cập nhật học sinh",
                "error"   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = $this->studentService->findStudentWithUser($id);
        $student->delete(); // Tự động xóa luôn user vì bạn đã setup trong booted()

        return response()->json([
            "status" => true,
            "message" => 'Xóa học sinh thành công',
            "data" => null
        ], 200);
    }
}
