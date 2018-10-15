<?php

namespace App\Modules\Users\Repositories;

use App\Repositories\AbstractEloquentRepository;
use Illuminate\Support\Facades\DB;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepository
{
    protected $primaryKey = 'username';
}
