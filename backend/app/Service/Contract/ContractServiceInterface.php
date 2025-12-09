<?php

namespace App\Service\Contract;

use App\Service\ServiceInterface;

interface ContractServiceInterface extends ServiceInterface
{

  public function getContractWithStudent($columns, $keyword, $perPage);

   public function createContract($data);

}

