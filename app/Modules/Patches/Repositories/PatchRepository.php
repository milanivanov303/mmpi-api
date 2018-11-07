<?php

namespace App\Modules\Patches\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Modules\Patches\Models\Patch;

class PatchRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * PatchRepository constructor
     *
     * @param Patch $model
     */
    public function __construct(Patch $model)
    {
        $this->model = $model;
    }
}
