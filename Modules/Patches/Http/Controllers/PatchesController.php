<?php

namespace Modules\Patches\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Patches\Repositories\PatchRepository;

class PatchesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PatchRepository $repository
     * @return void
     */
    public function __construct(PatchRepository $repository)
    {
        $this->repository = $repository;
    }
}
