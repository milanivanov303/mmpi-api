<?php

namespace App\Modules\Issues\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Issues\Repositories\IssueRepository;

class IssuesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param IssueRepository $model
     * @return void
     */
    public function __construct(IssueRepository $model)
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
     * Update the specified issue.
     *
     * @param  Request  $request
     * @param  string  $tts_id
     * @return Response
     */
    public function update(Request $request, $tts_id)
    {
        return $this->output(
            $this->model->update($request->json()->all(), $tts_id)
        );
    }

    /**
     * Delete the specified issue.
     *
     * @param  string  $tts_id
     * @return Response
     */
    public function delete($tts_id)
    {
        try {
            $this->model->delete($tts_id);
            return response('Issue deleted successfully', 204);
        } catch (\Exception $exception) {
            return response('Issue could not be deleted', 400);
        }
    }

    /**
     * Retrieve the issue for the given id.
     *
     * @param  string  $username
     * @return Response
     */
    public function getOne($username)
    {
        return $this->output(
            $this->model->find($username)
        );
    }

    /**
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
