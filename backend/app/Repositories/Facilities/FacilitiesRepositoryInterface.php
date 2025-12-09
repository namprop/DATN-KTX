<?php

namespace App\Repositories\Facilities;

use App\Repositories\RepositoryInterface;

interface FacilitiesRepositoryInterface extends RepositoryInterface

{

    public function createFacility($data);



    public function getAllFacilityAndRoom($columns, $keyword, $perPage);

}
