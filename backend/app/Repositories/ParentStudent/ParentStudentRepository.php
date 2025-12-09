<?php


namespace App\Repositories\ParentStudent;

use App\Repositories\BaseRepository;

use App\Models\ParentStudent;
use App\Models\Student;
use App\Models\User;

class ParentStudentRepository extends BaseRepository implements ParentStudentRepositoryInterface
{
    public function getModel()
    {
        return ParentStudent::class;
    }

    public function getAllParentStudentWithUserAndStudent($columns, $keyword, $perPage)
    {
        return ParentStudent::with(['user', 'student'])
            ->where(function ($q) use ($columns, $keyword) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%$keyword%");
                }
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $keyword]);
    }

    public function findParenStudentWithUserAndStudent($id)
    {
        return ParentStudent::with(['user','student'])->findOrFail($id);
    }

    public function createParentStudent($data)
    {

        $student = Student::where('student_code', $data['student_code'])->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'Parent',
        ]);

        $parentStudent = ParentStudent::create([
            'user_id'      => $user->id,
            'student_id' => $student->id,
            'full_name'    => $data['full_name'],
            'phone'        => $data['phone'] ?? null,
            'address'       => $data['address'],
            'gender'       => $data['gender'] ?? null,

        ]);
        return $parentStudent;
    }
}
