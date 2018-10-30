<?php

namespace App\Repositories;

use App\Helpers\DataFilter;
use App\Models\Model;
use App\Helpers\Mapper;
use App\Helpers\ModelFilter;

abstract class AbstractRepository
{
    /**
     * Eloquent model
     *
     * @var Model
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

    protected function getWith()
    {
        $visible = $this->model->getVisible();

        // if no visible fields set return all relations
        if (empty($visible)) {
            return $this->with;
        }

        // return only relations that are needed
        return array_intersect(
            $this->with,
            $visible
        );
    }

    /**
     * Get all records
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $filters = [])
    {
        $builder = $this->setFilters($filters);
        return $builder->with($this->getWith())->get();
    }

    /**
     * Get paginated records
     *
     * @param integer|null $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, array $filters = [])
    {
        $builder = $this->setFilters($filters);
        return $builder->with($this->getWith())->paginate($perPage);
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        $this->model->fill($data)->save();
        $this->model->load($this->getWith());

        return $this->model;
    }

    /**
     * Update existing record
     *
     * @param array $data
     * @param mixed $id
     * @return Model
     */
    public function update(array $data, $id)
    {
        $model = $this->find($id);

        $model->fill($data)->save();
        $model->load($this->getWith());

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
     * @return Model
     */
    public function find($id)
    {
        return $this->model->with($this->getWith())->where($this->primaryKey, $id)->firstOrFail();
    }

    /**
     * Set model filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function setFilters($filters)
    {
        if (array_key_exists('fields', $filters)) {
            $this->setVisible($filters['fields']);
        }

        return (new ModelFilter($this->model))->getBuilder($filters);
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
