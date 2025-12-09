<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Service\ParentStudent\ParentStudentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ParentStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $parentStudent;

    public function __construct(ParentStudentServiceInterface $parentStudent)
    {
        return $this->parentStudent = $parentStudent;
    }
    public function index(Request $request)
    {
        //
        $keyword = $request->query('search');

        $columns = ['full_name'];

        $parentStudent = $this->parentStudent->getAllParentStudentWithUserAndStudent($columns, $keyword, 8);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách học sinh thành công',
            'data' => $parentStudent->items(),
            'pagination' => [
                'current_page' => $parentStudent->currentPage(),
                'per_page' => $parentStudent->perPage(),
                'total' => $parentStudent->total(),
                'last_page' => $parentStudent->lastPage(),
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
        //
        $validator = Validator::make(
            $request->all(),
            [
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|min:6',
                'full_name'     => 'required|string|max:255',
                'phone'         => 'required|string|max:15',
                'address'       => 'required|string|max:255',
                'student_code'  => 'nullable|exists:students,student_code' ?? null,
                'gender'        => 'nullable|string|max:255',
            ],
            [
                'name.required'          => 'Tên đăng nhập không được để trống',
                'email.required'         => 'Email không được để trống',
                'email.email'            => 'Email không đúng định dạng',
                'email.unique'           => 'Email đã tồn tại trong hệ thống',
                'password.required'      => 'Mật khẩu không được để trống',
                'password.min'           => 'Mật khẩu phải có ít nhất 6 ký tự',
                'full_name.required'     => 'Họ tên không được để trống',
                'student_code.required'  => 'Mã học sinh không được để trống',
                'student_code.exists'    => 'Mã học sinh không tồn tại trong hệ thống',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            DB::beginTransaction();

            $parentStudent = $this->parentStudent->createParentStudent($data, $request);

            DB::commit();

            return response()->json([
                "status"  => true,
                "message" => 'Tạo người dùng thành công',
                "data"    => $parentStudent
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status"  => false,
                "message" => "Có lỗi xảy ra khi tạo người dùng",
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

        $parentStudent = $this->parentStudent->findParenStudentWithUserAndStudent($id);

        if (!$parentStudent) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin nhân viên thành công',
            'data' => $parentStudent
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string|max:255',
                'phone'     => 'nullable|string|max:15',
                'address'  => 'nullable|string|max:255',
                'gender'  => 'nullable|string|max:255',
                'student_code'  => 'nullable|exists:students,student_code',
            ],
            [
                'full_name.required' => 'Họ tên không được để trống',
            ]


        );

           if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors(),
            ], 422);
        }

           $data = request()->all();

        $staff = $this->parentStudent->update($data, $id);

        return response()->json([
            "status" => true,
            "message" => 'Cập nhật nhân viên thành công',
            "data" => null
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
