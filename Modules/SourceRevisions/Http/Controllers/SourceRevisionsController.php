<?php

namespace Modules\SourceRevisions\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SourceRevisions\Repositories\SourceRevisionRepository;

class SourceRevisionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SourceRevisionRepository $repository
     * @return void
     */
    public function __construct(SourceRevisionRepository $repository)
    {
        $this->repository = $repository;
    }
}
