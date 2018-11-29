<?php

namespace Modules\Projects\Repositories;

use Modules\Core\Repositories\AbstractRepository;
use Modules\Core\Repositories\RepositoryInterface;
use Modules\Projects\Models\Project;

class ProjectRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'name';

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
