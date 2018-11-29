<?php

namespace Modules\Projects\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Projects\Repositories\ProjectRepository;

class ProjectsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectRepository $model
     * @return void
     */
    public function __construct(ProjectRepository $model)
    {
        $this->model = $model;
    }
}
