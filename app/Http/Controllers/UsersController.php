<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Traits\Filterable;

class UsersController extends Controller
{
    use Filterable;

    /**
     * The user model instance.
     */
    protected $model;

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
     * Retrieve users list.
     *
     * @param Request $request
     * @return Response
     */

    /**
     * @param Request $request
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function many(Request $request)
    {
        //$users = Cache::get('users');

        $filters = $this->getFilters($request);

        //return $this->model->where($filters)->get();

        //Cache::put('users', [1,2,3], 10);

        return $this->model->where($filters)->paginate(2);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $user = new User($request->json()->all());
        if ($user->saveOrFail()) {
            return response()->json($user, 201);
        }
    }

    /**
     * Update the specified user.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //$this->validate($request, [
        //    'name' => 'required',
        //    'email' => 'required|email|unique:users'
        //]);

        $user = $this->model->findOrFail($id);

        $user->fill($request->json()->all());

        if ($user->saveOrFail()) {
            return response()->json($user);
        }
    }

    /**
     * Delete the specified user.
     *
     * @param  string  $id
     * @return Response
     */
    public function delete($id)
    {
        $this->model->destroy($id);
        return response('Deleted', 204);
    }

    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }
}
