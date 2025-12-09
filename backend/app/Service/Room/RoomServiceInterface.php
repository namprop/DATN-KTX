<?php

namespace App\Service\Room;

use App\Service\ServiceInterface;

interface RoomServiceInterface extends ServiceInterface
{

    public function getALlRoomWithUser($id);

    public function getAllAndCountStudent($columns, $keywords,$perPage,$filters = []);

    public function updateRoomStatus($room, $studentCount);


}

