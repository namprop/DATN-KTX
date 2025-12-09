<?php

namespace App\Service\DepartureRequest;

use App\Service\ServiceInterface;

interface DepartureRequestServiceInterface extends ServiceInterface
{

    public function getDepartureRequestWithStudent();

    public function createDepartureRequest($data);

}

