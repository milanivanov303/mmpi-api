<?php

namespace Modules\InstanceTypes\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Instances\Models\InstanceType;

class InstanceTypeRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * InstanceTypeRepository constructor
     *
     * @param InstanceType $model
     */
    public function __construct(InstanceType $model)
    {
        $this->model = $model;
    }
}
