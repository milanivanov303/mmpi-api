<?php

namespace Modules\Installations\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Installations\Models\Installation;

class InstallationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * InstallationRepository constructor
     *
     * @param Installation $model
     */
    public function __construct(Installation $model)
    {
        $this->model = $model;
    }
}
