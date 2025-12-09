<?php


namespace App\Repositories\Room;

use App\Repositories\BaseRepository;

use App\Models\Room;
use App\Models\Student;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    public function getModel()
    {
        return Room::class;
    }

    public function getALlRoomWithUser($id)
    {
        return Room::withCount('students')   // đếm số sinh viên
            ->with('students')        // nếu muốn lấy danh sách chi tiết
            ->findOrFail($id);
    }

    public function getAllAndCountStudent($columns, $keywords, $perPage, $filters = [])
    {
        $query = Room::withCount('students')
            ->where(function ($q) use ($columns, $keywords) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$keywords}%");
                }
            });

        // ✅ Chỉ cho phép filter theo các cột hợp lệ
        $allowedFilters = ['status', 'description'];

        foreach ($filters as $column => $value) {
            if (in_array($column, $allowedFilters) && !empty($value) && $value !== 'all') {
                $query->whereRaw("LOWER($column) = ?", [strtolower($value)]);
            }
        }

        return $query
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(array_merge(['search' => $keywords], $filters));
    }
}
