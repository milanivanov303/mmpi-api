<?php

namespace App\Modules\Patches\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Patches\Repositories\PatchRepository;

class PatchesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PatchRepository $model
     * @return void
     */
    public function __construct(PatchRepository $model)
    {
        $this->model = $model;
    }
}
