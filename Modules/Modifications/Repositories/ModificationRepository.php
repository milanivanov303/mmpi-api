<?php

namespace Modules\Modifications\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Modifications\Models\Modification;

class ModificationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectRepository constructor
     *
     * @param Modification $model
     */
    public function __construct(Modification $model)
    {
        $this->model = $model;
    }
}
