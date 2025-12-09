<?php

namespace App\Service\Contract;

use App\Repositories\Contract\ContractRepositoryInterface;
use App\Service\BaseService;

class ContractService extends BaseService implements ContractServiceInterface
{
    public $repository;

    public function __construct(ContractRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getContractWithStudent($columns, $keyword, $perPage){
        return $this->repository->getContractWithStudent($columns, $keyword, $perPage);
    }

    public function createContract($data){
        return $this->repository->createContract($data);
    }
}
