<?php

namespace Modules\Installations\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Installations\Repositories\InstallationRepository;

class InstallationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstallationRepository $model
     * @return void
     */
    public function __construct(InstallationRepository $model)
    {
        $this->model = $model;
    }
}
