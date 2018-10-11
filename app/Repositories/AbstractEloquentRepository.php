<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepository
{
    /**
     * Eloquent model
     * 
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Filters to apply on model
     * @var array
     */
    protected $filters = [];

    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @param Model $model
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * Get repository model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get all records
     * 
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = array('*'))
    {
        return $this->model->get($columns);
    }

    /**
     * Get paginated records
     *
     * @param integer $perPage
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Create new record
     * 
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update existing record
     * 
     * @param array $data
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, $id)
    {
        return $this->model->where($this->primaryKey, $id)->update($data);
    }

    /**
     * Delete record
     * 
     * @param type $id
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->where($this->primaryKey, $id)->destroy();
    }

    /**
     * Get single record
     * 
     * @param mixed $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = array('*'))
    {
        return $this->model->where($this->primaryKey, $id)->firstOrFail($columns);
    }

    /**
     * Set model filters
     * 
     * @param array $filters
     */
    public function setFilters($filters)
    {
        if (method_exists($this->model, 'setFilters')) {
            $this->model = $this->model->setFilters($filters);
        }
    }
}
