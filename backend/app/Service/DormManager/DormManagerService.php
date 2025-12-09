<?php

namespace App\Service\DormManager;

use App\Repositories\DormManager\DormManagerRepositoryInterface;
use App\Service\BaseService;

class DormManagerService extends BaseService implements DormManagerServiceInterface
{
    public $repository;

    public function __construct(DormManagerRepositoryInterface $repository)
    {
       return $this->repository = $repository;
    }

    public function getAllDormManagerWithUser($column, $keyword, $perPage)
    {
       return $this->repository->getAllDormManagerWithUser($column, $keyword, $perPage);

    }

    public function findDormManagerWithUser($id)
    {
       return $this->repository->findDormManagerWithUser($id);
    }

    public function createDormManager($data)
    {
       return $this->repository->createDormManager($data);
    }
}
