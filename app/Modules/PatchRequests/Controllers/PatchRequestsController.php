<?php

namespace App\Modules\PatchRequests\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\PatchRequests\Repositories\PatchRequestRepository;

class PatchRequestsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PatchRequestRepository $model
     * @return void
     */
    public function __construct(PatchRequestRepository $model)
    {
        $this->model = $model;
    }
}
