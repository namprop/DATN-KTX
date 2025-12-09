<?php

namespace App\Service\DepartureRequest;

use App\Repositories\DepartureRequest\DepartureRequestRepositoryInterface;
use App\Service\BaseService;

class DepartureRequestService extends BaseService implements DepartureRequestServiceInterface
{
    public $repository;

    public function __construct(DepartureRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getDepartureRequestWithStudent()
    {
        return $this->repository->getDepartureRequestWithStudent();
    }

    public function createDepartureRequest($data){
        return $this->repository->createDepartureRequest($data);
    }
}
