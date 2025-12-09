<?php

namespace App\Repositories\Student;

use App\Repositories\RepositoryInterface;

interface StudentRepositoryInterface extends RepositoryInterface

{

    public function getAllStudentsWithUser($columns, $keyword, $perPage);


    public function createStudent($data, $request);

    public function updateStudent($id, $data, $request);

    public function findStudentWithUser($id);
}
