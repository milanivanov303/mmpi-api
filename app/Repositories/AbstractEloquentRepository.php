<?php

namespace App\Repositories;

abstract class AbstractEloquentRepository
{
    protected $model;
    protected $primaryKey = 'id';

    public function __construct($model) {
        $this->model = $model;
    }

    public function all($columns = array('*'))
    {
        return $this->model->all($columns);
    }
    
    public function paginate($perPage = 15, $columns = array('*'))
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->where($this->primaryKey, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->model->where($this->primaryKey, $id)->destroy();
    }

    public function find($id, $columns = array('*'))
    {
        return $this->model->where($this->primaryKey, $id)->firstOrFail($columns);
    }
}
