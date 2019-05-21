<?php

namespace Modules\ProjectEvents\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ProjectEvents\Repositories\ProjectEventRepository;

class ProjectEventsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectEventRepository $repository
     * @return void
     */
    public function __construct(ProjectEventRepository $repository)
    {
        $this->repository = $repository;
    }
}
