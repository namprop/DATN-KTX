<?php


namespace App\Repositories\Facilities;

use App\Repositories\BaseRepository;

use App\Models\Facilities;
use App\Models\Room;

class FacilitiesRepository extends BaseRepository implements FacilitiesRepositoryInterface
{
    public function getModel()
    {
        return Facilities::class;
    }

    public function getAllFacilityAndRoom($columns, $keyword, $perPage)
    {
        return Facilities::with('room')
            ->where(function ($q) use ($columns, $keyword) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', "%$keyword%");
                }
            })
            ->orderBy('room_id', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $keyword]);
    }

    public function createFacility($data)
    {
        $room = Room::where('room_code', $data['room_code'])->first();

        $facility = $this->create([
            'facility_name' => $data['facility_name'],
            'facility_code' => $data['facility_code'],
            'quantity' => $data['quantity'],
            'status' => $data['status'] ?? 'good',
            'is_student_device' => $data['is_student_device'] ?? false,
            'room_id' => $room ? $room->id : null,
        ]);

        return $facility;
    }
}
