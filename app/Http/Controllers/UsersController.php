<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->model = $user;
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
        if ($request->input('page')) {
            $data = $this->model->setFilters($request)->paginate($request->input('per_page'));
        } else {
            $data = $this->model->setFilters($request)->get();
        }

        return $this->output($data);
    }
}
