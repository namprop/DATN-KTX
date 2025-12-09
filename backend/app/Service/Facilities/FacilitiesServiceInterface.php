<?php

namespace App\Service\Facilities;

use App\Service\ServiceInterface;

interface FacilitiesServiceInterface extends ServiceInterface
{

    public function createFacility($data);
    public function getAllFacilityAndRoom($columns, $keyword, $perPage);


}

