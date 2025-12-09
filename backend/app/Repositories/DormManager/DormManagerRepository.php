<?php


namespace App\Repositories\DormManager;

use App\Repositories\BaseRepository;

use App\Models\DormManager;
use App\Models\User;

class DormManagerRepository extends BaseRepository implements DormManagerRepositoryInterface
{
    public function getModel()
    {
        return DormManager::class;
    }

    public function getAllDormManagerWithUser($columns, $keyword, $perPage)
    {
        return DormManager::with(['user'])
            ->where(function ($q) use ($columns, $keyword) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $keyword]);
    }


       public function findDormManagerWithUser($id)
    {
        return DormManager::with('user')->findOrFail($id);
    }


    public function createDormManager($data)
    {


        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'DormManager',
        ]);

        $dormmanager = DormManager::create([
            'user_id'      => $user->id,
            'full_name'    => $data['full_name'],
            'phone'        => $data['phone'] ?? null,
            'position'       => $data['position'] ?? 'Nhân Viên Quản Lý',
        ]);

        return $dormmanager;
    }




}
