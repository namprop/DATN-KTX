<?php

namespace App\Service\Student;

use App\Service\ServiceInterface;

interface StudentServiceInterface extends ServiceInterface
{

    public function getAllStudentsWithUser($columns, $keyword,$perPage);

    public function createStudent($data, $request);

    public function updateStudent($id, $data, $request);

    public function findStudentWithUser($id);

}

