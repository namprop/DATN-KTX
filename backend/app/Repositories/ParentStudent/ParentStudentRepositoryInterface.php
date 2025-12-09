<?php

namespace App\Repositories\ParentStudent;

use App\Repositories\RepositoryInterface;

interface ParentStudentRepositoryInterface extends RepositoryInterface

{

    public function getAllParentStudentWithUserAndStudent($columns,$keyword,$perPage);
    public function findParenStudentWithUserAndStudent($id);
    public function createParentStudent($data);


}
