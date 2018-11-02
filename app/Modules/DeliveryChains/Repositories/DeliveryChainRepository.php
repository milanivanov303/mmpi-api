<?php

namespace App\Modules\DeliveryChains\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Modules\DeliveryChains\Models\DeliveryChain;

class DeliveryChainRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectRepository constructor
     *
     * @param DeliveryChain $model
     */
    public function __construct(DeliveryChain $model)
    {
        $this->model = $model;
    }
}
