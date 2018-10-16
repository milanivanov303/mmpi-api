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
     * @param User $model
     * @return void
     */
    public function __construct(UserRepository $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the hash for the given revision.
     *
     * @param  string  $username
     * @return Response
     */
    public function getOne($username)
    {
        return $this->output(
            $this->model->where('username', $username)->firstOrFail()
        );
    }

    /**
     *
     * @param Request $request
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getMany(Request $request)
    {
        $this->model->setFilters($request->all());

        if ($request->input('page')) {
            $data = $this->model->paginate($request->input('per_page'));
        } else {
            $data = $this->model->all();
        }

        return $this->output($data);
    }
}
