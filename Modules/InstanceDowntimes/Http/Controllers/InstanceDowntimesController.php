<?php

namespace Modules\InstanceDowntimes\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\InstanceDowntimes\Repositories\InstanceDowntimeRepository;

class InstanceDowntimesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstanceDowntimeRepository $repository
     * @return void
     */
    public function __construct(InstanceDowntimeRepository $repository)
    {
        $this->repository = $repository;
    }
}
