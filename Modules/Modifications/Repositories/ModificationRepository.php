<?php

namespace Modules\Modifications\Repositories;

use Modules\Core\Repositories\AbstractRepository;
use Modules\Core\Repositories\RepositoryInterface;
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
