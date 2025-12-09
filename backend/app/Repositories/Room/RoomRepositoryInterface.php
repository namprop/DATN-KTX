<?php

namespace App\Repositories\Room;

use App\Repositories\RepositoryInterface;

interface RoomRepositoryInterface extends RepositoryInterface

{

    public function getALlRoomWithUser($id);

    public function getAllAndCountStudent($columns, $keywords,$perPage,$filters = []);


}
