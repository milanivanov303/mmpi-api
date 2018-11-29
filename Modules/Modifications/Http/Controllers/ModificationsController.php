<?php

namespace Modules\Modifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Modifications\Repositories\ModificationRepository;

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
