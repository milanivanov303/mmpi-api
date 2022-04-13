<?php

namespace Modules\DeliveryChainTypes\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\DeliveryChains\Models\DeliveryChainType;

class DeliveryChainTypeRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * DeliveryChainTypeRepository constructor
     *
     * @param DeliveryChainType $model
     */
    public function __construct(DeliveryChainType $model)
    {
        $this->model = $model;
    }
}
