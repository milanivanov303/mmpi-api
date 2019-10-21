<?php

namespace Modules\PojectEventEstimations\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PojectEventEstimations\Repositories\PojectEventEstimationRepository;

class PojectEventEstimationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PojectEventEstimationRepository $repository
     * @return void
     */
    public function __construct(PojectEventEstimationRepository $repository)
    {
        $this->repository = $repository;
    }
}
