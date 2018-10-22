<?php

namespace App\Modules\Hashes\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Hashes\Repositories\HashRepository;

/**
 * Manage hashes
 *
 */
class HashesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param HashRepository $model
     * @return void
     */
    public function __construct(HashRepository $model)
    {
        $this->model = $model;
    }

    /**
     * Create new hash
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        return $this->output(
            $this->model->create($request->json()->all()),
            201
        );
    }

    /**
     * Update the specified hash.
     *
     * @param  Request  $request
     * @param  string  $hash_rev
     * @return Response
     */
    public function update(Request $request, $hash_rev)
    {
        return $this->output(
            $this->model->update($request->json()->all(), $hash_rev)
        );
    }
    
    /**
     * Delete the specified user.
     *
     * @param  string  $hash_rev
     * @return Response
     */
    public function delete($hash_rev)
    {
        $this->model->delete($hash_rev);
        return response('Hash deleted successfully', 204);
    }

    /**
     * Retrieve the hash for the given revision.
     *
     * @param  int  $hash_rev
     * @return Response
     */
    public function getOne($hash_rev)
    {
        return $this->output(
            $this->model->find($hash_rev)
        );
    }
    
    /**
     * Retrieve hashes list.
     *
     * @param Request $request
     * @return Response
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
