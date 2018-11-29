<?php

namespace Modules\PatchRequests\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\PatchRequests\Repositories\PatchRequestRepository;

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
