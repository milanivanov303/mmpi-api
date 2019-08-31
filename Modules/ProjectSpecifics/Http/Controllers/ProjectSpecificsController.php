<?php

namespace Modules\ProjectSpecifics\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ProjectSpecifics\Repositories\ProjectSpecificRepository;

class ProjectSpecificsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectSpecificRepository $repository
     * @return void
     */
    public function __construct(ProjectSpecificRepository $repository)
    {
        $this->repository = $repository;
    }
}
