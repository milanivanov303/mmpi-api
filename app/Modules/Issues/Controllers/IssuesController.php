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
}
