<?php

namespace App\Repositories\DepartureRequest;

use App\Repositories\RepositoryInterface;

interface DepartureRequestRepositoryInterface extends RepositoryInterface

{
    public function getDepartureRequestWithStudent();

    public function createDepartureRequest($data);

}
