<?php

use App\Core\Model;

abstract class BaseService
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(array $conditions = [], array $orderBy = [], int $limit = null, int $offset = null)
    {
        return $this->model->findAll($conditions, $orderBy, $limit, $offset);
    }

    public function getById(int $id)
    {
        return $this->model->findById($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->model->delete($id);
    }

    public function count(array $conditions = [])
    {
        return $this->model->count($conditions);
    }
}