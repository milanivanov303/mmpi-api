<?php

namespace App\Repositories;

use App\Models\Model;
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

    /**
     * Get model relations
     *
     * @return array
     */
    protected function getWith()
    {
        $with = array_merge(
            $this->with,
            $this->model->getWith()
        );

        $visible = $this->model->getVisible();

        // if no visible fields set return all relations
        if (empty($visible)) {
            return $with;
        }

        // return only relations that will be needed
        return array_intersect(
            $with,
            $visible
        );
    }

    /**
     * Get single record
     *
     * @param mixed $id
     * @param array $fields
     *
     * @return Model
     */
    public function find($id, array $fields = [])
    {
        $this->model->setVisible($fields);
        $this->model->setWith($this->getWith());

        return $this->model->where($this->primaryKey, urldecode($id))->firstOrFail();
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
     *
     * @throws \Throwable
     */
    public function create(array $data)
    {
        return $this->save($data);
    }

    /**
     * Update existing record
     *
     * @param array $data
     * @param mixed $id
     * @return Model
     *
     * @throws \Throwable
     */
    public function update(array $data, $id)
    {
        $this->model = $this->find($id);
        return $this->save($data);
    }

    /**
     * Save record
     *
     * @param array $data
     * @return Model
     *
     * @throws \Throwable
     */
    protected function save($data)
    {
        $this->model->fill($data)->saveOrFail();
        $this->model->load($this->getWith());

        return $this->model;
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
     * Set model filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function setFilters(array $filters)
    {
        if (array_key_exists('fields', $filters)) {
            $this->model->setVisible($filters['fields']);
        }

        return (new ModelFilter($this->model))->getBuilder($filters);
    }
}
