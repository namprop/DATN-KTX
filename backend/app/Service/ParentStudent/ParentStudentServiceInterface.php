<?php

namespace App\Service\ParentStudent;

use App\Service\ServiceInterface;

interface ParentStudentServiceInterface extends ServiceInterface
{

     public function getAllParentStudentWithUserAndStudent($columns,$keyword,$perPage);
    public function findParenStudentWithUserAndStudent($id);
    public function createParentStudent($data);

}

