<?php

namespace App\Repositories\Contract;

use App\Repositories\RepositoryInterface;

interface ContractRepositoryInterface extends RepositoryInterface

{
    public function getContractWithStudent($columns, $keyword, $perPage);

    public function createContract($data);

}
