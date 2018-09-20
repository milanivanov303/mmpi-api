<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Dependency;
use App\Traits\Filterable;

class DependenciesController extends Controller
{
    use Filterable;

    /**
     * The user model instance.
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param Dependency $model
     * @return void
     */
    public function __construct(Dependency $model)
    {
        $this->model = $model;
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
        $filters = $this->getFilters($request);
        
        return $this->model->where($filters)->get();
        
        return $this->model->where($filters)->paginate(2);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $user = new Dependency($request->json()->all());
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
        
        $dependency = $this->model->findOrFail($id);
        
        $dependency->fill($request->json()->all());

        if ($dependency->saveOrFail()) {
            return response()->json($dependency);
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
