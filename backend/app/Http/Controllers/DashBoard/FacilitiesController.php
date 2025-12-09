<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Facilities;
use App\Service\Facilities\FacilitiesServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $facilitiesService;

    public function __construct(FacilitiesServiceInterface $facilitiesService)
    {
        //
        $this->facilitiesService = $facilitiesService;
    }


    public function index(Request $request)
    {
        //

        $keyword = $request->query('search', '');
        $status = $request->query('status', '');
        $perPage = 8;

        $query = Facilities::with('room');

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('facility_name', 'like', "%{$keyword}%")
                    ->orWhere('facility_code', 'like', "%{$keyword}%")
                    ->orWhereHas('room', function ($roomQuery) use ($keyword) {
                        $roomQuery->where('room_code', 'like', "%{$keyword}%");
                    });
            });
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        $facilities = $query->orderBy('facility_code', 'asc')->paginate($perPage);

        return response()->json(
            [
                "status" => true,
                "message" => 'Lấy danh sách đồ dùng thành công',
                "data" => $facilities->items(),
                "pagination" => [
                    'total' => $facilities->total(),
                    'per_page' => $facilities->perPage(),
                    'current_page' => $facilities->currentPage(),
                    'last_page' => $facilities->lastPage(),
                ],
            ],
            200
        );
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
                "facility_name" => 'required|string|max:255',
                'facility_code' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'status' => 'nullable|in:good,broken',
                'is_student_device' => 'nullable|boolean',
                'room_code' => 'nullable|exists:rooms,room_code',
            ],
            [
                'facility_name.required' => 'Tên đồ dùng không được để trống',
                'facility_code.required' => 'Mã đồ dùng không được để trống',
                'facility_code.unique' => 'Mã đồ dùng đã tồn tại',
                'quantity.required' => 'Số lượng không được để trống',
                'quantity.integer' => 'Số lượng phải là số nguyên',
                'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1',
                'status.in' => 'Trạng thái không hợp lệ',
                'is_student_device.boolean' => 'is_student_device phải là kiểu boolean',
                'room_code.exists' => 'Phòng không tồn tại',

            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $request->all();

        $facility = $this->facilitiesService->createFacility($data);

        return response()->json([
            'status' => true,
            'message' => 'Tạo đồ dùng thành công',
            'data' => $facility
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
        //

        $facility = $this->facilitiesService->find($id);

        $validator = Validator::make(
            $request->all(),
            [
                "facility_name" => 'required|string|max:255',
                'facility_code' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'status' => 'nullable|in:good,broken',
                'is_student_device' => 'nullable|boolean',
                'room_code' => 'nullable|exists:rooms,room_code',
            ],
            [
                'facility_name.required' => 'Tên đồ dùng không được để trống',
                'facility_code.required' => 'Mã đồ dùng không được để trống',
                'facility_code.unique' => 'Mã đồ dùng đã tồn tại',
                'quantity.required' => 'Số lượng không được để trống',
                'quantity.integer' => 'Số lượng phải là số nguyên',
                'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1',
                'status.in' => 'Trạng thái không hợp lệ',
                'is_student_device.boolean' => 'is_student_device phải là kiểu boolean',
                'room_code.exists' => 'Phòng không tồn tại',

            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $request->all();

        $facility = $this->facilitiesService->update($data, $id);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật đồ dùng thành công',
            'data' => $facility
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $facility = $this->facilitiesService->find($id);

        if (!$facility) {
            return response()->json(
                [
                    "status" => false,
                    "message" => 'Đồ dùng không tồn tại',
                    "data" => null
                ],
                404
            );
        }

        $this->facilitiesService->delete($id);

        return response()->json(
            [
                "status" => true,
                "message" => 'Xóa đồ dùng thành công',
                "data" => null
            ],
            200
        );
    }
}
