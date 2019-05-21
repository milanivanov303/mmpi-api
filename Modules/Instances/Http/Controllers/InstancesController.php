<?php

namespace Modules\Instances\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Instances\Repositories\InstanceRepository;

class InstancesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstanceRepository $repository
     * @return void
     */
    public function __construct(InstanceRepository $repository)
    {
        $this->repository = $repository;
    }
}
