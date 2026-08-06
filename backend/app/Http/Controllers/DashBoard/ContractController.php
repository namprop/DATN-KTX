<?php

namespace App\Http\Controllers\DashBoard;

use App\Enums\ContractStatus;
use App\Http\Controllers\Controller;
use App\Service\Contract\ContractServiceInterface;
use App\Service\User\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{

    protected $contractService;

    public function __construct(ContractServiceInterface $contractService)
    {
        $this->contractService = $contractService;
    }




    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $columns = ['student.full_name', 'student.student_code'];
        $keyword = $request->query('search');

        $contracts = $this->contractService->getContractWithStudent($columns, $keyword, 20);

        return response()->json([
            "status" => true,
            "message" => 'Lấy danh sách hợp đồng thành công',
            "data" => $contracts->items(),
            "pagination" => [
                "total" => $contracts->total(),
                "per_page" => $contracts->perPage(),
                "current_page" => $contracts->currentPage(),
                "last_page" => $contracts->lastPage(),
            ]
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        // Nếu người dùng nhập dd/mm/yyyy => chuyển thành yyyy-mm-dd
        if (!empty($input['start_date'])) {
            $input['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['start_date'])->format('Y-m-d');
        }
        if (!empty($input['end_date'])) {
            $input['end_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['end_date'])->format('Y-m-d');
        }

        // Sau đó validate như bình thường
        $validate = Validator::make(
            $input,
            [

                'start_date' => 'required|date|date_format:Y-m-d',
                'end_date'   => 'required|date|after:start_date|date_format:Y-m-d',
                'status' => ['nullable', Rule::enum(ContractStatus::class)],
                'student_code' => 'required|exists:students,student_code|unique:contracts,student_id,NULL,id,student_id,(select id from students where student_code = ' . ($input['student_code'] ?? '') . ')',
            ],
            [

                'student_code.required' => 'Mã sinh viên không được để trống',
                'student_code.exists' => 'Mã sinh viên không tồn tại',
                'student_code.unique' => 'Sinh viên này đã có hợp đồng',
                'start_date.required' => 'Ngày bắt đầu không được để trống',
                'end_date.required' => 'Ngày kết thúc không được để trống',
                'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "status" => false,
                "message" => 'Dữ liệu không hợp lệ',
                "errors" => $validate->errors()

            ], 422);
        }

        // Lưu contract
        $contract = $this->contractService->createContract($input);

        return response()->json([
            "status" => true,
            "message" => 'Thêm hợp đồng thành công',
            "data" => $contract,
        ], 201);
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
        $contract = $this->contractService->find($id);

        if (!$contract) {
            return response()->json([
                "status" => false,
                "message" => "Hợp đồng không tồn tại"
            ], 404);
        }

        $input = $request->all();

        // 🧠 Xử lý định dạng ngày từ dd/mm/yyyy → Y-m-d
        foreach (['start_date', 'end_date'] as $field) {
            if (!empty($input[$field])) {
                try {
                    $input[$field] = \Carbon\Carbon::createFromFormat('d/m/Y', $input[$field])->format('Y-m-d');
                } catch (\Exception $e) {
                    return response()->json([
                        "status" => false,
                        "message" => "Ngày {$field} không đúng định dạng (dd/mm/yyyy)"
                    ], 400);
                }
            }
        }

        // 🧾 Validate dữ liệu
        $validate = Validator::make(
            $input,
            [
                'start_date' => 'required|date|date_format:Y-m-d',
                'end_date'   => 'required|date|after:start_date|date_format:Y-m-d',
                'status' => ['nullable', Rule::enum(ContractStatus::class)],
            ],
            [
                'start_date.required' => 'Ngày bắt đầu không được để trống',
                'start_date.date' => 'Ngày bắt đầu không đúng định dạng',
                'end_date.required' => 'Ngày kết thúc không được để trống',
                'end_date.date' => 'Ngày kết thúc không đúng định dạng',
                'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
                'status.in' => 'Trạng thái hợp đồng không hợp lệ',
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validate->errors()->first(),
            ], 400);
        }


        // 🔄 Tự động cập nhật trạng thái theo ngày kết thúc
        if (!empty($input['end_date'])) {
            $today = now()->toDateString();

            if ($input['end_date'] < $today) {
                // Hợp đồng đã hết hạn
                $input['status'] = 'Completed';
            } else {
                // Hợp đồng còn hiệu lực
                $input['status'] = 'Active';
            }
        }


        // 🧩 Cập nhật dữ liệu
        $contract = $this->contractService->update($input, $id);

        return response()->json([
            "status" => true,
            "message" => "Cập nhật hợp đồng thành công",
            "data" => $contract
        ], 200);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $deleted = $this->contractService->delete($id);

        if (!$deleted) {
            return response()->json(
                ["status" => false, "message" => "Xóa thất bại hoặc hợp đồng không tồn tại"],
                404
            );
        }

        return response()->json(
            ["status" => true, "message" => "Xóa hợp đồng thành công"],
            200
        );
    }
}
