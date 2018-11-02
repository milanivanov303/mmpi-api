<?php

namespace App\Modules\Projects\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Projects\Repositories\ProjectRepository;

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
