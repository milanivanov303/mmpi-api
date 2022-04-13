<?php

namespace Modules\InstanceTypes\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\InstanceTypes\Repositories\InstanceTypeRepository;

class InstanceTypesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstanceTypeRepository $repository
     * @return void
     */
    public function __construct(InstanceTypeRepository $repository)
    {
        $this->repository = $repository;
    }
}
