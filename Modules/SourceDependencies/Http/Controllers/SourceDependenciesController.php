<?php

namespace Modules\SourceDependencies\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SourceDependencies\Repositories\SourceDependencyRepository;

class SourceDependenciesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SourceDependencyRepository $repository
     * @return void
     */
    public function __construct(SourceDependencyRepository $repository)
    {
        $this->repository = $repository;
    }
}
