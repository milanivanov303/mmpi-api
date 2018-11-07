<?php

namespace App\Modules\Hashes\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Modules\Hashes\Repositories\HashRepository;

/**
 * Manage hashes
 *
 */
class HashesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param HashRepository $model
     * @return void
     */
    public function __construct(HashRepository $model)
    {
        $this->model = $model;
    }
}
