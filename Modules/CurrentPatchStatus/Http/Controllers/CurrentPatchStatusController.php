<?php

namespace Modules\CurrentPatchStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CurrentPatchStatus\Repositories\CurrentPatchStatusRepository;

class CurrentPatchStatusController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param CurrentPatchStatusRepository $repository
     * @return void
     */
    public function __construct(CurrentPatchStatusRepository $repository)
    {
        $this->repository = $repository;
    }
}
