<?php

namespace Modules\ProjectEvents\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\ProjectEvents\Exports\ProjectEventsExport;
use Modules\ProjectEvents\Imports\ProjectEventsImport;
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

    /**
     * Import project event xlsx
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request) : JsonResponse
    {
        if (!$request->hasFile('project_events_excel')) {
            $data['error'] = "No valid file to import.";
            return response()->json($data, 422);
        }

        $project = $request->input('project');
        $file    = $request->file('project_events_excel')->store('imports');

        $import = new ProjectEventsImport($project);
        $import->import($file);

        if ($import->failures()->isNotEmpty()) {
            // to send mail with failiures
        }

        return response()->json($import, 200);
    }
}
