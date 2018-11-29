<?php

namespace Modules\Modifications\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
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
