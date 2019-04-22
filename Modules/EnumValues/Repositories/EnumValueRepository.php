<?php

namespace Modules\EnumValues\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use App\Models\EnumValue;

class EnumValueRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
 
    protected $primaryKey = ['type', 'key'];

    /**
     * EnumValueRepository constructor
     *
     * @param EnumValue $model
     */
    public function __construct(EnumValue $model)
    {
        $this->model = $model;
    }
}
