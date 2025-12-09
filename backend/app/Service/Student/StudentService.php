<?php

namespace App\Service\Student;

use App\Repositories\Student\StudentRepositoryInterface;
use App\Service\BaseService;

class StudentService extends BaseService implements StudentServiceInterface
{
    public $repository;

    public function __construct(StudentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllStudentsWithUser($columns, $keyword,$perPage)
    {
        return $this->repository->getAllStudentsWithUser($columns, $keyword,$perPage);
    }

    public function createStudent($data, $request)
    {
        return $this->repository->createStudent($data, $request);
    }

    public function updateStudent($id, $data, $request)
    {
        return $this->repository->updateStudent($id, $data, $request);
    }

    public function findStudentWithUser($id)
    {
        return $this->repository->findStudentWithUser($id);
    }


}

