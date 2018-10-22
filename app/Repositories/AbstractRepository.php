<?php

namespace App\Repositories;

use App\Helpers\DataFilter;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Mapper;
use App\Helpers\ModelFilter;

abstract class AbstractRepository
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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

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
        return $this->model->with($this->with)->get();
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
        return $this->model->with($this->with)->paginate($perPage);
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $this->model->fill($data)->save();
        $this->model->load($this->with);

        return $this->model;
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
        $model = $this->find($id);

        $model->fill($data)->save();
        $model->load($this->with);

        return $model;
    }

    /**
     * Delete record
     *
     * @param type $id
     * @return boolean
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        $model = $this->find($id);
        return $model->delete();
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
        return $this->model->with($this->with)->where($this->primaryKey, $id)->firstOrFail($columns);
    }

    /**
     * Set model filters
     *
     * @param array $filters
     */
    public function setFilters($filters)
    {
        if (array_key_exists('fields', $filters)) {
            $this->model->setVisible($filters['fields']);
        }

        $this->model = (new ModelFilter($this->model))->getBuilder($filters);
    }

    /**
     * Set model visible columns
     *
     * @param string|array $fields
     */
    protected function setVisible($fields)
    {
        if (is_string($fields)) {
            $fields = array_map('trim', explode(',', $fields));
        }

        $this->model->setVisible($fields);
    }
}
