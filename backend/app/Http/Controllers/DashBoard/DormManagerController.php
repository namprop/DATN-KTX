<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Service\DormManager\DormManagerServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DormManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $dormmanagerService;

    public function __construct(
        DormManagerServiceInterface $dormmanagerService
    ) {
        $this->dormmanagerService = $dormmanagerService;
    }

    public function index(Request $request)
    {
        //
        $keyword = $request->query('search');
        $column = ['full_name'];

        $dormmanager = $this->dormmanagerService->getAllDormManagerWithUser($column, $keyword, 10);
        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách ban quản lý hệ thống thành công',
            'data' => $dormmanager->items(),
            'pagination' => [
                'current_page' => $dormmanager->currentPage(),
                'per_page' => $dormmanager->perPage(),
                'total' => $dormmanager->total(),
                'last_page' => $dormmanager->lastPage(),
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
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|min:6',
                'full_name' => 'required|string|max:255',
                'phone'     => 'nullable|string|max:15',
                'position'  => 'nullable|string|max:255',
            ],
            [
                'name.required'      => 'Tên đăng nhập không được để trống',
                'email.required'     => 'Email không được để trống',
                'email.email'        => 'Email không đúng định dạng',
                'email.unique'       => 'Email đã tồn tại trong hệ thống',
                'password.required'  => 'Mật khẩu không được để trống',
                'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự',
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

        try {
            // chỉ lấy dữ liệu đã qua validate
            $data = $validator->validated();

            // Transaction: nếu lỗi rollback toàn bộ
            DB::beginTransaction();

            $dormmanager = $this->dormmanagerService->createDormManager($data, $request);

            DB::commit();

            return response()->json([
                "status"  => true,
                "message" => 'Tạo ban quản lý hệ thống thành công',
                "data"    => $dormmanager
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"  => false,
                "message" => "Có lỗi xảy ra khi tạo ban quản lý hệ thống",
                "error"   => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $dormmanager = $this->dormmanagerService->findDormManagerWithUser($id);

            if (!$dormmanager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy ban quản lý hệ thống',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin ban quản lý hệ thống thành công',
                'data' => $dormmanager
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu ban quản lý hệ thống',
                'error'   => $e->getMessage()
            ], 500);
        }
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
                'position'  => 'nullable|string|max:255',
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

        $dormmanager = $this->dormmanagerService->update($data, $id);

        return response()->json([
            "status" => true,
            "message" => 'ban quản lý hệ thống thành công',
            "data" => null
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        $dormmanager = $this->dormmanagerService->findDormManagerWithUser($id);
        $dormmanager->delete(); // Tự động xóa luôn user vì bạn đã setup trong booted()

        return response()->json([
            "status" => true,
            "message" => 'Xóa ban quản lý hệ thống thành công',
            "data" => null
        ], 200);
    }
}
