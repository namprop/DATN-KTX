<?php

namespace App\Service\Room;


use App\Repositories\Room\RoomRepositoryInterface;
use App\Service\BaseService;

class RoomService extends BaseService implements RoomServiceInterface
{
    public $repository;

    public function __construct(RoomRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getALlRoomWithUser($id)
    {
        return $this->repository->getALlRoomWithUser($id);
    }

    public function getAllAndCountStudent($columns, $keywords,$perPage,$filters = [])
    {
        return $this->repository->getAllAndCountStudent($columns, $keywords,$perPage);
    }

       public function updateRoomStatus($room, $studentCount)
    {
        if ($room->status === 'Maintenance') {
            return;
        }
        $desiredStatus = 'Available';
        if ((int) $room->capacity <= 0) {
            return;
        }

        if ($studentCount >= $room->capacity) {
            $desiredStatus = 'Full';
        } else {
            $desiredStatus = 'Available';
        }

        if ($room->status !== $desiredStatus) {
            $room->update(['status' => $desiredStatus]);
        }
    }
}
