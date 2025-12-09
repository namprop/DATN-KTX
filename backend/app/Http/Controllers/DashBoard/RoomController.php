<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Service\Room\RoomServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $roomService;

    public function __construct(RoomServiceInterface $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $description = $request->query('description', '');
        $perPage = 20;

        $query = Room::withCount('students');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('room_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($description) && $description !== 'all') {
            $query->where('description', $description);
        }

        $rooms = $query->orderBy('room_code', 'asc')->paginate($perPage);

        foreach ($rooms as $room) {
            $newStatus = $room->status;

            if ($room->status !== 'Maintenance') { // không tự đổi nếu đang bảo trì
                if ($room->students_count >= $room->capacity) {
                    $newStatus = 'Full';
                } elseif ($room->students_count == 0) {
                    $newStatus = 'Available';
                } else {
                    $newStatus = 'Available'; // vẫn còn chỗ
                }

                // nếu trạng thái khác trong DB thì update lại
                if ($newStatus !== $room->status) {
                    $room->update(['status' => $newStatus]);
                }
            }
        }

        return response()->json([
            "status" => true,
            "message" => "Lấy danh sách phòng thành công",
            "data" => $rooms->items(),
            "pagination" => [
                'total' => $rooms->total(),
                'per_page' => $rooms->perPage(),
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
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
                'room_code' => 'required|string|max:255|unique:rooms,room_code',
                'capacity' => 'required|integer|min:1',
                'status' => 'nullable|in:Available,Full,Maintenance',
                'description' => 'nullable|string|max:255',
                'price' => 'nullable|string|max:255',
            ],
            [
                'room_code.required' => 'Mã phòng không được để trống',
                'room_code.string' => 'Mã phòng phải là chuỗi ký tự',
                'room_code.max' => 'Mã phòng không được vượt quá 255 ký tự',
                'room_code.unique' => 'Mã phòng đã tồn tại',
                'capacity.required' => 'Sức chứa không được để trống',
                'capacity.integer' => 'Sức chứa phải là số nguyên',
                'capacity.min' => 'Sức chứa phải lớn hơn hoặc bằng 1',
                'status.in' => 'Trạng thái phòng không hợp lệ',
                'description.string' => 'Mô tả phải là chuỗi ký tự',
                'description.max' => 'Mô tả không được vượt quá 255 ký tự',
                'price.string' => 'Giá phải là chuỗi ký tự',
                'price.max' => 'Giá không được vượt quá 255 ký tự',
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
        $room = $this->roomService->create($data);
        return response()->json(
            [
                "status" => true,
                "message" => 'Thêm phòng thành công',
                "data" => $room
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $room = $this->roomService->getALlRoomWithUser($id);

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin phòng thành công',
            'data' => $room
        ], 200);
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

        $room = $this->roomService->find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'room_code' => 'required|string|max:255|unique:rooms,room_code,' . $id,
                'capacity' => 'required|integer|min:1',
                'status' => 'nullable|in:Available,Full,Maintenance',
                'description' => 'nullable|string|max:255',
                'price' => 'nullable|string|max:255',


            ],
            [
                'room_code.required' => 'Mã phòng không được để trống',
                'room_code.string' => 'Mã phòng phải là chuỗi ký tự',
                'room_code.max' => 'Mã phòng không được vượt quá 255 ký tự',
                'room_code.unique' => 'Mã phòng đã tồn tại',
                'capacity.required' => 'Sức chứa không được để trống',
                'capacity.integer' => 'Sức chứa phải là số nguyên',
                'capacity.min' => 'Sức chứa phải lớn hơn hoặc bằng 1',
                'status.in' => 'Trạng thái phòng không hợp lệ',
                'description.string' => 'Mô tả phải là chuỗi ký tự',
                'description.max' => 'Mô tả không được vượt quá 255 ký tự',
                'price.string' => 'Giá phải là chuỗi ký tự',
                'price.max' => 'Giá không được vượt quá 255 ký tự',
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
        $room = $this->roomService->update($data, $id);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật phòng thành công',
            'data' => $room
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        $room = $this->roomService->find($id);
        if (!$room) {
            return response()->json(
                [
                    "status" => false,
                    "message" => 'Phòng không tồn tại',
                    "data" => null
                ],
                404
            );
        }
        if ($room->students()->count() > 0) {
            return response()->json(
                [
                    "status" => false,
                    "message" => 'Phòng đang có sinh viên, không thể xóa',
                    "data" => null
                ],
                400
            );
        }
        $this->roomService->delete($id);
    }
}
