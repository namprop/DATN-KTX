<?php

namespace App\Repositories;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    abstract public function getModel();

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        $object = $this->model->find($id);
        return $object->update($data);
    }

    public function delete(int $id)
    {

        $object = $this->model->find($id);

        return $object->delete();
    }

    public function SearchAndPaginate($columns, $keyword, $perPage)
    {
        return $this->model
            ->where(function ($q) use ($columns, $keyword) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $keyword]);
    }
}
