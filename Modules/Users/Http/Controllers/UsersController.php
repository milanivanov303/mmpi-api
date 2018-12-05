<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Users\Repositories\UserRepository;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserRepository $model
     * @return void
     */
    public function __construct(UserRepository $model)
    {
        $this->model = $model;
    }
}
