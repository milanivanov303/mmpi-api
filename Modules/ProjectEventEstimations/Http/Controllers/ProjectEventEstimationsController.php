<?php

namespace Modules\ProjectEventEstimations\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ProjectEventEstimations\Repositories\ProjectEventEstimationRepository;

class ProjectEventEstimationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectEventEstimationRepository $repository
     * @return void
     */
    public function __construct(ProjectEventEstimationRepository $repository)
    {
        $this->repository = $repository;
    }
}
