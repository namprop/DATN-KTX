<?php

namespace App\Service\DormManager;

use App\Service\ServiceInterface;

interface DormManagerServiceInterface extends ServiceInterface
{

    public function getAllDormManagerWithUser($columns, $keyword, $perPage);
    public function findDormManagerWithUser($id);
    public function createDormManager($data);
}
