<?php

namespace Modules\Patches\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Patches\Models\Patch;

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
