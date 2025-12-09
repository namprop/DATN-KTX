<?php

namespace App\Service\ParentStudent;

use App\Repositories\ParentStudent\ParentStudentRepositoryInterface;
use App\Service\BaseService;

class ParentStudentService extends BaseService implements ParentStudentServiceInterface
{
    public $repository;

    public function __construct(ParentStudentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllParentStudentWithUserAndStudent($columns, $keyword, $perPage)
    {
        return  $this->repository->getAllParentStudentWithUserAndStudent($columns, $keyword, $perPage);
    }

    public function findParenStudentWithUserAndStudent($id)
    {
        return $this->repository->findParenStudentWithUserAndStudent($id);
    }

    public function createParentStudent($data)
    {
        return $this->repository->createParentStudent($data);
    }
}
