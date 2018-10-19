<?php

namespace App\Modules\Users\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Models\User;

class UserRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'username';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'department',
        'accessGroup',
        'manager',
        'deputy'
    ];

    /**
     * UserRepository constructor
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

}
