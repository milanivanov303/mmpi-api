<?php

namespace App\Modules\Projects\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Modules\Projects\Models\Project;

class ProjectRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'username';

    /**
     * ProjectRepository constructor
     *
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }
}
