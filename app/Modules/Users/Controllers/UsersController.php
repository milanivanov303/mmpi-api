<?php

namespace App\Modules\Users\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Users\Repositories\UserRepository;

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
