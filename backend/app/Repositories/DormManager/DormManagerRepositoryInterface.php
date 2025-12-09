<?php

namespace App\Repositories\DormManager;

use App\Repositories\RepositoryInterface;

interface DormManagerRepositoryInterface extends RepositoryInterface

{

    public function getAllDormManagerWithUser($columns, $keyword, $perPage);
    public function findDormManagerWithUser($id);
    public function createDormManager($data);

}
