<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param integer $status
     * @param array $meta
     * @return Response
     */
    public function output($data, $status = 200, $meta = [])
    {
        $output = [];

        if ($meta) {
            $output['meta'] = $meta;
        }

        if ($data instanceof \Illuminate\Pagination\AbstractPaginator) {
            $output['data'] = $data->items();
            $output['meta']['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ];
        } else {
            $output['data'] = $data;
        }

        return response()->json($output, $status);
    }

    /**
     * Create new
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            return $this->output(
                $this->model->create($request->json()->all()),
                201
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
     * @return Response
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
     * @return Response
     */
    public function getOne($id)
    {
        return $this->output(
            $this->model->find($id)
        );
    }

    /**
     *
     * @param Request $request
     * @return Response
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
