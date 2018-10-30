<?php

namespace App\Modules\PatchRequests\Repositories;

use App\Repositories\RepositoryInterface;
use App\Repositories\AbstractRepository;
use App\Modules\PatchRequests\Models\PatchRequest;

class PatchRequestRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'modifications',
        'patch'
    ];

    /**
     * PatchRequestRepository constructor
     *
     * @param PatchRequest $model
     */
    public function __construct(PatchRequest $model)
    {
        $this->model = $model;
    }
}
