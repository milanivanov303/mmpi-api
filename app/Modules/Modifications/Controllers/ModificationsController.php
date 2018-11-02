<?php

namespace App\Modules\Modifications\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Modifications\Repositories\ModificationRepository;

class ModificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ModificationRepository $model
     * @return void
     */
    public function __construct(ModificationRepository $model)
    {
        $this->model = $model;
    }
}
