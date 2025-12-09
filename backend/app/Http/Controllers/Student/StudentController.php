<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\DepartureRequest;
use App\Models\Facilities;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function showMyRoom()
    {
        $user = Auth::user();
        $student = $user->student; // lấy bản ghi student gắn với user

        if (!$student || !$student->room_id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa được phân phòng.'
            ], 404);
        }

        $room = Room::with(['students:id,full_name,student_code,room_id'])
            ->find($student->room_id);

        if (!$room) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phòng của bạn.'
            ], 404);
        }

        $room->current_count = $room->students->count();

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin phòng thành công.',
            'room' => $room,
        ]);
    }

    public function showMyPayments()
    {
        $user = Auth::user();
        $student = $user->student; // lấy bản ghi student gắn với user

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin sinh viên.'
            ], 404);
        }

        // Lấy tất cả hóa đơn của sinh viên (đã và chưa thanh toán)
        $payments = $student->payments()
            ->with(['room:id,room_code'])
            ->orderByDesc('created_at')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get([
                'payment_id',
                'payment_code',
                'room_id',
                'month',
                'year',
                'electricity_usage',
                'water_usage',
                'total_amount',
                'payment_status',
                'payment_date',
                'description',
                'created_at',
            ]);

        // Nếu chưa có hóa đơn nào
        if ($payments->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa có hóa đơn nào.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách hóa đơn thành công.',
            'payments' => $payments,
        ]);
    }

    // public function postDepartureRequest(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'reason'        => 'required|string|max:1000',
    //         'request_date'  => 'required|date_format:d/m/Y',
    //     ], [
    //         'reason.required'         => 'Lý do không được để trống',
    //         'reason.string'           => 'Lý do phải là chuỗi ký tự',
    //         'reason.max'              => 'Lý do không được vượt quá 1000 ký tự',
    //         'request_date.required'   => 'Ngày yêu cầu không được để trống',
    //         'request_date.date_format' => 'Ngày yêu cầu phải có định dạng d/m/Y',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => $validator->errors()->first(),
    //             "data" => null
    //         ], 400);
    //     }

    //     try {
    //         $user = Auth::user();
    //         if (!$user) {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "Người dùng chưa đăng nhập.",
    //                 "data" => null
    //             ], 401);
    //         }

    //         $student = $user->student;
    //         if (!$student) {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "Không tìm thấy thông tin sinh viên cho tài khoản này.",
    //                 "data" => null
    //             ], 404);
    //         }

    //         $requestDate = Carbon::createFromFormat('d/m/Y', $request->request_date)->format('Y-m-d');
    //         $today = Carbon::today()->format('Y-m-d');

    //         // ✅ Kiểm tra nếu đã gửi đơn cho cùng ngày
    //         $existingRequest = DepartureRequest::where('student_id', $student->id)
    //             ->whereDate('request_date', $requestDate)
    //             ->first();

    //         if ($existingRequest) {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "Bạn đã gửi yêu cầu rời KTX ngày này rồi.",
    //                 "data" => $existingRequest
    //             ], 400);
    //         }

    //         // ✅ Kiểm tra hóa đơn chưa thanh toán
    //         $unpaidPayments = Payment::where('student_id', $student->id)
    //             ->where('payment_status', '!=', 'paid')
    //             ->count();

    //         if ($unpaidPayments > 0) {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "Bạn vẫn còn hóa đơn chưa thanh toán, vui lòng thanh toán trước khi rời KTX.",
    //             ], 400);
    //         }

    //         // ✅ Kiểm tra hợp đồng còn hạn và cần confirm rời trước hạn
    //         $activeContract = Contract::where('student_id', $student->id)
    //             ->where('status', 'Active')
    //             ->whereDate('end_date', '>', $today)
    //             ->first();

    //         if ($activeContract && $requestDate < $today) {
    //             return response()->json([
    //                 "status" => true,
    //                 "message" => "Hợp đồng của bạn vẫn còn hiệu lực. Bạn có muốn rời sớm không?",
    //                 "need_confirm" => true
    //             ], 200);
    //         }

    //         // ✅ Nếu hôm nay là ngày rời hoặc không cần confirm → tạo đơn
    //         $status = $requestDate === $today ? 'Approved' : 'Pending';
    //         $approvedAt = $requestDate === $today ? now() : null;

    //         $departureRequest = DepartureRequest::create([
    //             'student_id'   => $student->id,
    //             'reason'       => $request->reason,
    //             'request_date' => $requestDate,
    //             'status'       => $status,
    //             'approved_at'  => $approvedAt,
    //         ]);

    //         // 🔹 Bổ sung logic giống update nếu Approved ngay hôm nay
    //         if ($status === 'Approved') {
    //             DB::beginTransaction();
    //             try {
    //                 // ✅ Cập nhật hợp đồng
    //                 Contract::where('student_id', $student->id)
    //                     ->update(['status' => 'Terminated']);

    //                 // ✅ Giải phóng phòng và trạng thái học sinh
    //                 $student->update([
    //                     'room_id' => null,
    //                     'status' => 'Inactive',
    //                 ]);

    //                 $oldRoomId = $student->room_id;

    //                 // ✅ Cập nhật trạng thái user
    //                 if ($student->user) {
    //                     $student->user->update(['status' => 'Inactive']);
    //                 }

    //                 // ✅ Hoàn tiền cọc nếu có
    //                 $depositPayment = Payment::where('student_id', $student->id)
    //                     ->where('description', 'LIKE', '%cọc%')
    //                     ->first();

    //                 if ($depositPayment) {
    //                     Payment::create([
    //                         'student_id'     => $student->id,
    //                         'room_id'        => $oldRoomId,
    //                         'payment_code'   => 'REFUND-' . strtoupper(uniqid()),
    //                         'total_amount'   => $depositPayment->total_amount,
    //                         'payment_status' => 'refund_pending',
    //                         'description'    => 'Hoàn trả tiền cọc ký túc xá',
    //                         'month'          => now()->format('m'),
    //                         'year'           => now()->format('Y'),
    //                     ]);
    //                 }

    //                 DB::commit();
    //             } catch (\Exception $e) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     "status" => false,
    //                     "message" => "Lỗi khi xử lý duyệt đơn hôm nay: " . $e->getMessage(),
    //                 ], 500);
    //             }
    //         }

    //         return response()->json([
    //             "status" => true,
    //             "message" => $status === 'Approved'
    //                 ? "Yêu cầu rời KTX của bạn đã được duyệt ngay hôm nay."
    //                 : "Tạo đơn xin rời KTX thành công. Hệ thống sẽ tự động duyệt khi đến ngày rời.",
    //             "data" => $departureRequest->load('student.user')
    //         ], 201);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Lỗi khi tạo đơn xin nghỉ: " . $e->getMessage(),
    //             "data" => null
    //         ], 500);
    //     }
    // }

    public function postDepartureRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason'        => 'required|string|max:1000',
            'request_date'  => 'required|date_format:d/m/Y',
        ], [
            'reason.required'         => 'Lý do không được để trống',
            'reason.string'           => 'Lý do phải là chuỗi ký tự',
            'reason.max'              => 'Lý do không được vượt quá 1000 ký tự',
            'request_date.required'   => 'Ngày yêu cầu không được để trống',
            'request_date.date_format' => 'Ngày yêu cầu phải có định dạng d/m/Y',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first(),
                "data" => null
            ], 400);
        }

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    "status" => false,
                    "message" => "Người dùng chưa đăng nhập.",
                    "data" => null
                ], 401);
            }

            $student = $user->student;
            if (!$student) {
                return response()->json([
                    "status" => false,
                    "message" => "Không tìm thấy thông tin sinh viên cho tài khoản này.",
                    "data" => null
                ], 404);
            }

            $requestDate = Carbon::createFromFormat('d/m/Y', $request->request_date)->format('Y-m-d');
            $today = Carbon::today()->format('Y-m-d');

            // Kiểm tra nếu đã gửi đơn cho cùng ngày
            $existingRequest = DepartureRequest::where('student_id', $student->id)
                ->whereDate('request_date', $requestDate)
                ->first();

            if ($existingRequest) {
                return response()->json([
                    "status" => false,
                    "message" => "Bạn đã gửi yêu cầu rời KTX ngày này rồi.",
                    "data" => $existingRequest
                ], 400);
            }

            // Kiểm tra hóa đơn chưa thanh toán
            $unpaidPayments = Payment::where('student_id', $student->id)
                ->where('payment_status', '!=', 'paid')
                ->count();

            if ($unpaidPayments > 0) {
                return response()->json([
                    "status" => false,
                    "message" => "Bạn vẫn còn hóa đơn chưa thanh toán, vui lòng thanh toán trước khi rời KTX.",
                ], 400);
            }

            // Kiểm tra hợp đồng còn hạn và cần confirm rời trước hạn
            $activeContract = Contract::where('student_id', $student->id)
                ->where('status', 'Active')
                ->whereDate('end_date', '>', $today)
                ->first();

            if ($activeContract && $requestDate < $today) {
                return response()->json([
                    "status" => true,
                    "message" => "Hợp đồng của bạn vẫn còn hiệu lực. Bạn có muốn rời sớm không?",
                    "need_confirm" => true
                ], 200);
            }

            // Xác định trạng thái đơn
            $status = $requestDate === $today ? 'Approved' : 'Pending';
            $approvedAt = $requestDate === $today ? now() : null;

            $departureRequest = DepartureRequest::create([
                'student_id'   => $student->id,
                'reason'       => $request->reason,
                'request_date' => $requestDate,
                'status'       => $status,
                'approved_at'  => $approvedAt,
            ]);

            // Nếu rời hôm nay → tự động xử lý giống admin duyệt
            if ($status === 'Approved') {
                DB::beginTransaction();
                try {
                    $oldRoomId = $student->room_id;

                    // Lấy hóa đơn tiền cọc nếu có
                    $depositPayment = Payment::where('student_id', $student->id)
                        ->where('description', 'LIKE', '%cọc%')
                        ->first();

                    // Cập nhật hợp đồng
                    Contract::where('student_id', $student->id)
                        ->update(['status' => 'Terminated']);

                    // Giải phóng phòng & cập nhật trạng thái học sinh
                    $student->update([
                        'room_id' => null,
                        'status' => 'Inactive',
                    ]);

                    // Cập nhật trạng thái user
                    if ($student->user) {
                        $student->user->update(['status' => 'Inactive']);
                    }

                    // Tạo phiếu hoàn tiền nếu có tiền cọc và room_id hợp lệ
                    if ($depositPayment && $oldRoomId) {
                        Payment::create([
                            'student_id'     => $student->id,
                            'room_id'        => $oldRoomId,
                            'payment_code'   => 'REFUND-' . strtoupper(uniqid()),
                            'total_amount'   => $depositPayment->total_amount,
                            'payment_status' => 'refund_pending',
                            'description'    => 'Hoàn trả tiền cọc ký túc xá',
                            'month'          => now()->format('m'),
                            'year'           => now()->format('Y'),
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        "status" => false,
                        "message" => "Lỗi khi xử lý duyệt đơn hôm nay: " . $e->getMessage(),
                    ], 500);
                }
            }

            return response()->json([
                "status" => true,
                "message" => $status === 'Approved'
                    ? "Yêu cầu rời KTX của bạn đã được duyệt ngay hôm nay và tiền cọc sẽ được hoàn."
                    : "Tạo đơn xin rời KTX thành công. Hệ thống sẽ tự động duyệt khi đến ngày rời.",
                "data" => $departureRequest->load('student.user')
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Lỗi khi tạo đơn xin rời: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }






    public function checkDepartureRequests()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    "status" => false,
                    "message" => "Người dùng chưa đăng nhập.",
                ], 401);
            }

            $student = $user->student;
            if (!$student) {
                return response()->json([
                    "status" => false,
                    "message" => "Không tìm thấy thông tin sinh viên.",
                ], 404);
            }

            $today = Carbon::today();

            // ✅ Tìm đơn chờ duyệt đúng ngày hiện tại
            $departure = DepartureRequest::where('student_id', $student->id)
                ->where('status', 'Pending')
                ->whereDate('request_date', $today)
                ->first();

            if (!$departure) {
                return response()->json([
                    "status" => true,
                    "message" => "Không có đơn cần duyệt hôm nay.",
                ], 200);
            }

            // ✅ Kiểm tra hóa đơn chưa thanh toán
            $unpaidPayments = Payment::where('student_id', $student->id)
                ->where('payment_status', '!=', 'paid')
                ->count();

            if ($unpaidPayments > 0) {
                return response()->json([
                    "status" => false,
                    "message" => "Bạn vẫn còn hóa đơn chưa thanh toán, vui lòng thanh toán trước khi rời KTX.",
                ], 400);
            }

            // ✅ Kiểm tra hợp đồng còn hạn
            $activeContract = Contract::where('student_id', $student->id)
                ->where('status', 'Active')
                ->whereDate('end_date', '>', $today)
                ->first();

            if ($activeContract) {
                return response()->json([
                    "status" => false,
                    "message" => "Hợp đồng của bạn vẫn còn hiệu lực. Bạn có muốn rời sớm không?",
                    "confirm_exit" => true, // để frontend confirm
                ], 200);
            }

            // ✅ Không còn nợ, hợp đồng hết hạn => duyệt tự động
            DB::beginTransaction();

            $departure->update([
                'status' => 'Approved',
                'approved_at' => now(),
            ]);

            Contract::where('student_id', $student->id)->update(['status' => 'Terminated']);
            $student->update(['room_id' => null, 'status' => 'Inactive']);

            if ($student->user) {
                $student->user->update(['status' => 'Inactive']);
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Đơn rời KTX của bạn đã được duyệt tự động hôm nay.",
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => false,
                "message" => "Lỗi khi kiểm tra đơn rời KTX: " . $e->getMessage(),
            ], 500);
        }
    }


    public function showMyContract()
    {
        $user = Auth::user();

        // Kiểm tra đăng nhập và có là sinh viên không
        if (!$user || !$user->student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sinh viên cho tài khoản này.',
            ], 404);
        }

        $student = $user->student;

        // Lấy hợp đồng gắn với sinh viên
        $contract = $student->contracts()
            ->with('student.room') // nếu muốn trả thêm thông tin sinh viên & user
            ->latest('created_at')
            ->first();

        if (!$contract) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa có hợp đồng nào.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin hợp đồng thành công.',
            'contract' => $contract,
        ]);
    }

    public function showRoomFacilities()
    {
        $user = Auth::user();
        $student = $user->student ?? null;

        if (!$student || !$student->room_id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa được phân phòng.'
            ], 404);
        }

        $facilities = Facilities::where('room_id', $student->room_id)
            ->get(['id', 'facility_code', 'facility_name', 'status']);

        return response()->json([
            'status' => true,
            'facilities' => $facilities,
        ]);
    }

    public function reportFacility(Request $request)
    {
        $user = Auth::user();
        $student = $user->student ?? null;

        if (!$student || !$student->room_id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa được phân phòng.'
            ], 404);
        }

        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'description' => 'nullable|string|max:500',
        ]);

        $facility = Facilities::where('id', $request->facility_id)
            ->where('room_id', $student->room_id)
            ->first();

        if (!$facility) {
            return response()->json([
                'status' => false,
                'message' => 'Thiết bị không thuộc phòng của bạn.',
            ], 403);
        }

        // Cập nhật status là hỏng
        $facility->status = 'broken';
        $facility->save();

        return response()->json([
            'status' => true,
            'message' => 'Báo cáo thiết bị hỏng thành công!',
            'facility' => $facility,
        ], 200);
    }

    public function checkOverduePayments()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Người dùng chưa đăng nhập.'
            ], 401);
        }

        $student = $user->student;
        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sinh viên.'
            ], 404);
        }

        $now = \Carbon\Carbon::now();

        // Đếm hóa đơn quá hạn
        $overdueCount = $student->payments()
            ->where('payment_status', '!=', 'paid')
            ->whereNotNull('payment_date')
            ->get()
            ->filter(fn($p) => \Carbon\Carbon::parse($p->payment_date)->lt($now))
            ->count();

        // Đếm tất cả hóa đơn chưa thanh toán
        $unpaidCount = $student->payments()
            ->where('payment_status', '!=', 'paid')
            ->count();

        // Nếu quá 3 hóa đơn chưa thanh toán → trạng thái “bị khóa”
        $locked = $unpaidCount >= 3;

        if (!in_array($student->status, ['Graduated', 'Inactive'])) {

            if ($overdueCount > 0 || $locked) {
                $student->status = 'Violate';   // chuyển vi phạm
            } else {
                $student->status = 'Active';    // tự phục hồi nếu hết nợ
            }

            $student->save();
        }

        return response()->json([
            'status' => true,
            'overdue_count' => $overdueCount,
            'unpaid_count' => $unpaidCount,
            'locked' => $locked,
            'message' => $locked
                ? 'Bạn có quá nhiều hóa đơn chưa thanh toán. Tài khoản bị hạn chế thao tác.'
                : null
        ]);
    }


    public function checkContractStatus()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Người dùng chưa đăng nhập.'
            ], 401);
        }

        $student = $user->student;
        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sinh viên.'
            ], 404);
        }

        $now = \Carbon\Carbon::now();

        // Lấy hợp đồng gần nhất
        $contract = $student->contracts()->latest('end_date')->first();

        if (!$contract) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa có hợp đồng nào.'
            ], 404);
        }

        $endDate = \Carbon\Carbon::parse($contract->end_date);
        $daysOverdue = ceil($now->diffInDays($endDate, false)); // âm nếu quá hạn

        $alertMessage = null;
        $locked = false;

        if ($daysOverdue < 0) {
            $alertMessage = "Hợp đồng của bạn đã quá hạn " . abs($daysOverdue) . " ngày!";
            // Nếu quá 2 tháng (60 ngày) → khóa sinh viên
            if (abs($daysOverdue) > 30) {
                $locked = true;
                $alertMessage .= " Tài khoản bị hạn chế thao tác do quá hạn lâu.";
            }
        }

        return response()->json([
            'status' => true,
            'contract_end_date' => $contract->end_date,
            'days_overdue' => $daysOverdue,
            'locked' => $locked,
            'alert' => $alertMessage
        ]);
    }

    public function extendContract(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->student) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sinh viên cho tài khoản này.',
            ], 404);
        }

        $student = $user->student;

        $request->validate([
            'end_date' => 'required|date|after_or_equal:today',
        ], [
            'end_date.required' => 'Ngày kết thúc mới không được để trống.',
            'end_date.date'     => 'Ngày kết thúc mới không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc mới phải là hôm nay hoặc sau hôm nay.',
        ]);

        $contract = $student->contracts()->latest('end_date')->first();
        if (!$contract) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chưa có hợp đồng nào.',
            ], 404);
        }

        $now = \Carbon\Carbon::today();
        $contractEnd = \Carbon\Carbon::parse($contract->end_date);

        $daysLeft = $contractEnd->diffInDays($now, false); // âm nếu quá hạn

        if ($daysLeft > 60) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ có thể gia hạn khi hợp đồng đã hết hạn hoặc còn 15 ngày.',
            ], 403);
        }

        $contract->update([
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gia hạn hợp đồng thành công.',
            'contract' => $contract,
        ]);
    }
}
