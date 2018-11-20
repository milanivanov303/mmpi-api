<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\ResourceModel;

class Controller extends BaseController
{
    /**
     * The model instance.
     */
    protected $model;

    /**
     * Get output response
     *
     * @param mixed $data
     *
     * @return JsonResource
     */
    public function output($data)
    {
        if ($data instanceof \App\Models\Model) {
            return new ResourceModel($data);
        }

        return new ResourceCollection($data);
    }

    /**
     * Create new
     *
     * @param Request $request
     *
     * @return JsonResource
     */
    public function create(Request $request)
    {
        try {
            return $this->output(
                $this->model->create($request->json()->all())
            );
        } catch (\Exceprion $e) {
            return response('Could not be created', 400);
        }
    }

    /**
     * Update
     *
     * @param  Request  $request
     * @param  mixed  $id
     *
     * @return JsonResource
     */
    public function update(Request $request, $id)
    {
        try {
            return $this->output(
                $this->model->update($request->json()->all(), $id)
            );
        } catch (\Exceprion $e) {
            return response('Could not be saved', 400);
        }
    }

    /**
     * Delete
     *
     * @param  mixed  $id
     * @return Response
     */
    public function delete($id)
    {
        try {
            $this->model->delete($id);
            return response('Deleted successfully', 204);
        } catch (\Exception $e) {
            // TODO: add more detaild error message here
            //       could define messages in controller and check error number
            return response('Could not be deleted', 400);
        }
    }

    /**
     * Retrieve by id.
     *
     * @param  mixed  $id
     *
     * @return JsonResource
     */
    public function getOne(Request $request, $id)
    {
        return $this->output(
            $this->model->find($id, $request->input('fields', []))
        );
    }

    /**
     *
     * @param Request $request
     *
     * @return JsonResource
     */
    public function getMany(Request $request)
    {
        if ($request->input('page')) {
            $data = $this->model->paginate($request->input('per_page'), $request->all());
        } else {
            $data = $this->model->all($request->all());
        }

        return $this->output($data);
    }
}
