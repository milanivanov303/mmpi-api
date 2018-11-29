<?php

namespace Modules\Instances\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Instances\Models\Instance;

class InstanceRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectRepository constructor
     *
     * @param Instance $model
     */
    public function __construct(Instance $model)
    {
        $this->model = $model;
    }

    /**
     * Save Instance
     *
     * @param array $data
     * @return Instance
     *
     * @throws \Throwable
     */
    protected function save($data)
    {
        $this->model->fill($data);

        $this->model->owner()->associate($data['owner']['id']);
        $this->model->status()->associate($data['status']['id']);
        $this->model->environmentType()->associate($data['environment_type']['id']);
        $this->model->instanceType()->associate(
            isset($data['instance_type']) ? $data['instance_type']['id'] : null
        );

        $this->model->saveOrFail();

        $this->model->load($this->getWith());

        return $this->model;
    }
}
