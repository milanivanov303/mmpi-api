<?php

namespace Modules\PatchRequestSpecifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PatchRequestSpecifications\Repositories\PatchRequestSpecificationRepository;

class PatchRequestSpecificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PatchRequestSpecificationRepository $repository
     * @return void
     */
    public function __construct(PatchRequestSpecificationRepository $repository)
    {
        $this->repository = $repository;
    }
}
