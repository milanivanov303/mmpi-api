<?php

namespace Modules\ProjectEvents\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ProjectEvents\Exports\ProjectEventsExport;
use Modules\ProjectEvents\Repositories\ProjectEventRepository;

class ProjectEventsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectEventRepository $repository
     * @return void
     */
    public function __construct(ProjectEventRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Export and return file
     *
     * @param int $year
     * @return void
     */
    public function export(int $year)
    {
        return new ProjectEventsExport($year);
    }
}
