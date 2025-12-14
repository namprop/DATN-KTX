<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\SchoolStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index(Request $request)
{
    $search = $request->query('search', '');
    $perPage = 30;

    $query = SchoolStudent::query();

    if (!empty($search)) {
        $query->where('full_name', 'like', "%{$search}%");
    }

    $schoolStudents = $query->paginate($perPage);

    return response()->json([
        "status" => true,
        "message" => 'Lấy danh sách học sinh thành công',
        "data" => $schoolStudents->items(),
        "pagination" => [
            'total' => $schoolStudents->total(),
            'per_page' => $schoolStudents->perPage(),
            'current_page' => $schoolStudents->currentPage(),
            'last_page' => $schoolStudents->lastPage(),
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
                "full_name"     => "required|string|max:225",
                "student_code"  => "required|unique:students,student_code",
                "gender"        => "nullable|in:Male,Female,Other",
            ],
            [
                "full_name.required"     => "Họ tên không được để trống",
                "student_code.required"  => "Mã học sinh không được để trống",
                "student_code.unique"    => "Mã học sinh đã tồn tại",
                "gender.in"              => "Giới tính chỉ nhận Male, Female hoặc Other",
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $schoolStudent = SchoolStudent::create($data);

        return response()->json(
            [
                "status" => true,
                "message" => 'Thêm học sinh thành công',
                "data" => $schoolStudent
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $schoolStudent = SchoolStudent::find($id);
        $validator = Validator::make(
            $request->all(),
            [
                "full_name"     => "required|string|max:225",
                "student_code"  => "required|unique:students,student_code," . $id,
                "gender"        => "nullable|in:Male,Female,Other",
            ],
            [
                "full_name.required"     => "Họ tên không được để trống",
                "student_code.required"  => "Mã học sinh không được để trống",
                "student_code.unique"    => "Mã học sinh đã tồn tại",
                "gender.in"              => "Giới tính chỉ nhận Male, Female hoặc Other",
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $schoolStudent = $schoolStudent->update($data, $id);

        return response()->json(
            [
                "status" => true,
                "message" => 'Cập nhật học sinh thành công',
                "data" => $schoolStudent
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $schoolStudent = SchoolStudent::find($id);
        if (!$schoolStudent) {
            return response()->json(
                [
                    "status" => false,
                    "message" => 'Học sinh không tồn tại',
                    "data" => null
                ],
                404
            );
        }
        $schoolStudent->delete();
    }
}
