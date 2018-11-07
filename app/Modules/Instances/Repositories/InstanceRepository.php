<?php

namespace App\Modules\Instances\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Modules\Instances\Models\Instance;

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
}
