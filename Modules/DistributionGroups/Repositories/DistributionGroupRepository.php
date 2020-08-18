<?php

namespace Modules\DistributionGroups\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\DistributionGroups\Models\DistributionGroup;

class DistributionGroupRepository extends AbstractRepository implements RepositoryInterface
{

    /**
     * @var string
     */
    protected $primaryKey = 'distribution_groups_id';

    /**
     * @var string
     */
    protected $customUniqueKey = 'samaccountname';

    /**
     * DistributionGroupRepository constructor.
     * @param DistributionGroup $model
     */
    public function __construct(DistributionGroup $model)
    {
        $this->model = $model;
    }

}