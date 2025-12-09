<?php


namespace App\Repositories\DepartureRequest;

use App\Repositories\BaseRepository;

use App\Models\DepartureRequest;
use Illuminate\Support\Carbon;
use App\Models\Student;

class DepartureRequestRepository extends BaseRepository implements DepartureRequestRepositoryInterface
{
    public function getModel()
    {
        return DepartureRequest::class;
    }

    public function getDepartureRequestWithStudent()
    {
        return $this->model->with('student.user')->get();
    }

    public function createDepartureRequest($data){

        $student = Student::where('student_code', $data['student_code'])->first();

        $departureRequest = $this->model->create([
            'student_id' => $student->id,
            'reason' => $data['reason'],
            'request_date' => $data['request_date']
            ? Carbon::createFromFormat('d/m/Y', $data['request_date'])->format('Y-m-d')
            : null,
            'status' => $data['status'] ?? 'Pending',
        ]);

        return $departureRequest;

    }
}
