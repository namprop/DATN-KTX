<?php

namespace App\Http\Controllers\DashBoard;

use App\Enums\DepartureRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\DepartureRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartureRequestController extends Controller
{
    /**
     * Hiển thị danh sách đơn xin nghỉ (kèm thông tin học sinh + tài khoản)
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;

        $query = DepartureRequest::with('student.user');
        if (!empty($search)) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student  code', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $departureRequests = $query->orderBy('id', 'desc')->paginate($perPage);





        return response()->json([
            "status" => true,
            "message" => "Lấy danh sách đơn xin nghỉ thành công",
            "data" => $departureRequests->items(),
            'pagination' => [
                'total' => $departureRequests->total(),
                'current_page' => $departureRequests->currentPage(),
                'last_page' => $departureRequests->lastPage(),
                'per_page' => $departureRequests->perPage(),
            ]

        ]);
    }

    /**
     * Tạo mới đơn xin nghỉ
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "student_code"  => "required|exists:students,student_code",
            'reason'        => 'required|string|max:1000',
            'request_date'  => 'required|date_format:d/m/Y',
            'status'        => ['nullable', Rule::enum(DepartureRequestStatus::class)],
        ], [
            'student_code.required'   => 'Mã học sinh không được để trống',
            'student_code.exists'     => 'Học sinh không tồn tại',
            'reason.required'         => 'Lý do không được để trống',
            'reason.string'           => 'Lý do phải là chuỗi ký tự',
            'reason.max'              => 'Lý do không được vượt quá 1000 ký tự',
            'request_date.required'   => 'Ngày yêu cầu không được để trống',
            'request_date.date_format' => 'Ngày yêu cầu phải có định dạng d/m/Y',
            'status.in'               => 'Trạng thái không hợp lệ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first(),
                "data" => null
            ], 400);
        }

        try {
            $student = Student::where('student_code', $request->student_code)->first();

            if (!$student) {
                return response()->json([
                    "status" => false,
                    "message" => "Không tìm thấy học sinh có mã: {$request->student_code}",
                    "data" => null
                ], 404);
            }

            // Xử lý ngày: d/m/Y → Y-m-d
            $requestDate = Carbon::createFromFormat('d/m/Y', $request->request_date)->format('Y-m-d');

            $departureRequest = DepartureRequest::create([
                'student_id'   => $student->id,
                'reason'       => $request->reason,
                'request_date' => $requestDate,
                'status'       => $request->status ?? 'Pending',
            ]);

            return response()->json([
                "status" => true,
                "message" => "Tạo đơn xin nghỉ thành công",
                "data" => $departureRequest->load('student.user')
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Lỗi khi tạo đơn xin nghỉ: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    /**
     * Hiển thị chi tiết một đơn xin nghỉ
     */
    public function show(string $id)
    {
        $departureRequest = DepartureRequest::with('student.user')->find($id);

        if (!$departureRequest) {
            return response()->json([
                "status" => false,
                "message" => "Không tìm thấy đơn xin nghỉ",
                "data" => null
            ], 404);
        }

        return response()->json([
            "status" => true,
            "message" => "Lấy chi tiết đơn xin nghỉ thành công",
            "data" => $departureRequest
        ], 200);
    }

    /**
     * Cập nhật trạng thái đơn xin nghỉ (VD: duyệt hoặc từ chối)
     */
    // public function update(Request $request, string $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'status' => 'required|in:Approved,Rejected',
    //     ], [
    //         'status.required' => 'Trạng thái không được để trống',
    //         'status.in' => 'Trạng thái không hợp lệ',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => $validator->errors()->first(),
    //         ], 400);
    //     }

    //     $departureRequest = DepartureRequest::with('student.user')->find($id);
    //     if (!$departureRequest) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Không tìm thấy đơn xin nghỉ",
    //         ], 404);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // ✅ Cập nhật trạng thái đơn xin nghỉ
    //         $departureRequest->status = $request->status;
    //         $departureRequest->save();

    //         if ($request->status === 'Approved') {
    //             $student = $departureRequest->student;

    //             // ✅ Kiểm tra còn hóa đơn chưa thanh toán không
    //             $unpaidPayments = Payment::where('student_id', $student->id)
    //                 ->where('payment_status', '!=', 'paid')
    //                 ->count();

    //             if ($unpaidPayments > 0) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     "status" => false,
    //                     "message" => "Không thể duyệt vì học sinh vẫn còn hóa đơn chưa thanh toán.",
    //                 ], 400);
    //             }

    //             // ✅ Cập nhật trạng thái hợp đồng thay vì xóa
    //             Contract::where('student_id', $student->id)
    //                 ->update(['status' => 'Terminated']);

    //             // ❌ Không xóa Payment hoặc DepartureRequest khác
    //             // Giữ nguyên dữ liệu để tra cứu lịch sử

    //             // ✅ Cập nhật trạng thái học sinh và user
    //             $student->update([
    //                 'room_id' => null,
    //                 'status' => 'Inactive',
    //             ]);

    //             if ($student->user) {
    //                 $student->user->update(['status' => 'Inactive']);
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             "status" => true,
    //             "message" => $request->status === 'Approved'
    //                 ? "Đã duyệt đơn và cập nhật trạng thái học sinh thành Inactive."
    //                 : "Đã từ chối đơn xin nghỉ.",
    //             "data" => $departureRequest
    //         ], 200);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Lỗi khi cập nhật trạng thái: " . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::enum(DepartureRequestStatus::class)->only([
                DepartureRequestStatus::Approved,
                DepartureRequestStatus::Rejected,
            ])],
        ], [
            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first(),
            ], 400);
        }

        $departureRequest = DepartureRequest::with('student.user')->find($id);
        if (!$departureRequest) {
            return response()->json([
                "status" => false,
                "message" => "Không tìm thấy đơn xin nghỉ",
            ], 404);
        }

        if ($departureRequest->status !== DepartureRequestStatus::Pending) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn này đã được xử lý trước đó.',
            ], 409);
        }

        DB::beginTransaction();

        try {
            // ✅ Cập nhật trạng thái đơn xin nghỉ
            $departureRequest->update([
                'status' => $request->status,
                'approved_at' => $request->status === 'Approved' ? now() : null,
            ]);

            // ✅ Nếu đơn được duyệt
            if ($request->status === 'Approved') {
                $student = $departureRequest->student;

                if (!$student) {
                    DB::rollBack();
                    return response()->json([
                        "status" => false,
                        "message" => "Không tìm thấy học sinh liên kết với đơn này.",
                    ], 404);
                }

                // ✅ Kiểm tra còn hóa đơn chưa thanh toán không
                $unpaidPayments = Payment::where('student_id', $student->id)
                    ->where('payment_status', '!=', 'paid')
                    ->count();

                if ($unpaidPayments > 0) {
                    DB::rollBack();
                    return response()->json([
                        "status" => false,
                        "message" => "Không thể duyệt vì học sinh vẫn còn hóa đơn chưa thanh toán.",
                    ], 400);
                }

                // ✅ Lấy hóa đơn tiền cọc đầu tiên (nếu có)
                $depositPayment = Payment::where('student_id', $student->id)
                    ->where('description', 'LIKE', '%cọc%')
                    ->first();

                // ✅ Tạo bản ghi hoàn tiền (nếu có tiền cọc)
                if ($depositPayment) {
                    Payment::create([
                        'student_id'     => $student->id,
                        'room_id'        => $student->room_id,
                        'payment_code'   => 'REFUND-' . strtoupper(uniqid()),
                        'total_amount'   => $depositPayment->total_amount,
                        'payment_status' => 'refund_pending', // 💰 đang chờ hoàn tiền
                        'description'    => 'Hoàn trả tiền cọc ký túc xá',
                        'month'          => now()->format('m'),
                        'year'           => now()->format('Y'),
                    ]);
                }

                // ✅ Cập nhật trạng thái hợp đồng
                Contract::where('student_id', $student->id)
                    ->update(['status' => 'Terminated']);

                // ✅ Giải phóng phòng và cập nhật trạng thái học sinh
                $previousRoomId = $student->room_id;

                $student->update([
                    'room_id' => null,
                    'status' => 'Inactive',
                ]);

                if ($previousRoomId) {
                    Room::whereKey($previousRoomId)
                        ->where('status', 'Full')
                        ->update(['status' => 'Available']);
                }

                // ✅ Cập nhật trạng thái tài khoản user của học sinh
                if ($student->user) {
                    $student->user->update(['status' => 'Inactive']);
                }
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => $request->status === 'Approved'
                    ? "Đã duyệt đơn, cập nhật trạng thái học sinh thành Inactive và tạo phiếu hoàn tiền cọc."
                    : "Đã từ chối đơn xin nghỉ.",
                "data" => $departureRequest,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => false,
                "message" => "Lỗi khi cập nhật trạng thái: " . $e->getMessage(),
            ], 500);
        }
    }





    /**
     * Xóa đơn xin nghỉ
     */
    public function destroy(string $id)
    {
        $departureRequest = DepartureRequest::find($id);

        if (!$departureRequest) {
            return response()->json([
                "status" => false,
                "message" => "Không tìm thấy đơn xin nghỉ",
                "data" => null
            ], 404);
        }

        $departureRequest->delete();

        return response()->json([
            "status" => true,
            "message" => "Xóa đơn xin nghỉ thành công",
            "data" => null
        ], 200);
    }
}
