<?php

namespace App\Service\Facilities;

use App\Repositories\Facilities\FacilitiesRepositoryInterface;
use App\Service\BaseService;

class FacilitiesService extends BaseService implements FacilitiesServiceInterface
{
    public $repository;

    public function __construct(FacilitiesRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createFacility($data)
    {
        return $this->repository->createFacility($data);
    }

    public function getAllFacilityAndRoom($columns, $keyword, $perPage)
    {
        return $this->repository->getAllFacilityAndRoom($columns, $keyword, $perPage);
    }


}
