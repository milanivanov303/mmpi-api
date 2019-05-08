<?php

namespace Modules\Branches\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Branches\Repositories\BranchRepository;

class BranchesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param BranchRepository $repository
     * @return void
     */
    public function __construct(BranchRepository $repository)
    {
        $this->repository = $repository;
    }
}
