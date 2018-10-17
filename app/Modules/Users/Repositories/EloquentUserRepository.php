<?php

namespace App\Modules\Users\Repositories;

use App\Repositories\AbstractEloquentRepository;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepository
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
}
