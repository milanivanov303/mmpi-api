<?php

namespace Modules\Sources\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sources\Repositories\SourceRepository;

class SourcesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SourceRepository $repository
     * @return void
     */
    public function __construct(SourceRepository $repository)
    {
        $this->repository = $repository;
    }
}
