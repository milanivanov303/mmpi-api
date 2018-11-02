<?php

namespace App\Modules\Instances\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Instances\Repositories\InstanceRepository;

class InstancesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstanceRepository $model
     * @return void
     */
    public function __construct(InstanceRepository $model)
    {
        $this->model = $model;
    }
}
