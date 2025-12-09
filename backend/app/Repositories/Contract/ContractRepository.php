<?php


namespace App\Repositories\Contract;

use App\Repositories\BaseRepository;

use App\Models\Contract;
use App\Models\Student;

class ContractRepository extends BaseRepository implements ContractRepositoryInterface
{
    public function getModel()
    {
        return Contract::class;
    }

    // public function getContractWithStudent($columns, $keyword, $perPage)
    // {
    //     return Contract::with('student')
    //         ->where(
    //             function ($q) use ($columns, $keyword) {
    //                 foreach ($columns as $column) {
    //                     $q->orWhere($column, 'like', "%{$keyword}%");
    //                 }
    //             }
    //         )->orderBy('id', 'asc')
    //         ->paginate($perPage)
    //         ->appends(['search' => $keyword]);
    // }

    public function getContractWithStudent($columns, $keyword, $perPage)
    {
        $contracts = Contract::with('student')
            ->where(function ($q) use ($columns, $keyword) {
                foreach ($columns as $column) {
                    if (str_contains($column, 'student.')) {
                        $col = str_replace('student.', '', $column);
                        $q->orWhereHas('student', function ($studentQuery) use ($col, $keyword) {
                            $studentQuery->where($col, 'like', "%{$keyword}%");
                        });
                    } else {
                        $q->orWhere("contracts.$column", 'like', "%{$keyword}%");
                    }
                }
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $keyword]);

        // 🔄 Thêm trạng thái ảo, không lưu vào DB


        $contracts->getCollection()->transform(function ($contract) {


            $today = \Carbon\Carbon::today();
            $start = \Carbon\Carbon::parse($contract->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($contract->end_date)->endOfDay();

            if ($today->lt($start)) {
                $contract->status = 'Pending';
            } elseif ($today->between($start, $end)) {
                $contract->status = 'Active';
            } else {
                $contract->status = 'Completed';
            }

            return $contract;
        });

        return $contracts;
    }



    public function createContract($data)
    {
        // 🧩 1️⃣ Kiểm tra sinh viên có tồn tại không
        $student = Student::where('student_code', $data['student_code'] ?? null)->first();

        if (!$student) {
            // Ném exception để controller bắt được
            throw new \Exception("Mã sinh viên không tồn tại.");
        }

        // 🧩 2️⃣ Kiểm tra sinh viên đã có hợp đồng chưa
        $existing = Contract::where('student_id', $student->id)->first();
        if ($existing) {
            throw new \Exception("Sinh viên này đã có hợp đồng.");
        }

        // 🧩 3️⃣ Tạo mới hợp đồng
        $contract = Contract::create([
            'student_id'      => $student->id,
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'deposit_amount'  => $data['deposit_amount'] ?? 0,
            'status'          => $data['status'] ?? 'Pending',
        ]);

        return $contract;
    }
}
