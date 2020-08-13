<?php

namespace Modules\ProjectEvents\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\ProjectEvents\Exports\ProjectEventsExport;
use Modules\ProjectEvents\Imports\ProjectEventsImport;
use Modules\ProjectEvents\Repositories\ProjectEventRepository;
use Illuminate\Support\Facades\Mail;
use Modules\ProjectEvents\Mail\ImportEventsMail;

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
            return response()->json(["error" => "No valid file to import."], 422);
        }

        $project = json_decode($request->input('project'));
        
        if (!is_object($project)) {
            return response()->json(["error" => "No valid project data"], 422);
        }

        $file   = $request->file('project_events_excel')->store('imports');
        $import = new ProjectEventsImport($project);

        $import->import($file);

        if ($import->getErrors()) {
            Mail::queue((new ImportEventsMail($import->getErrors()))->onQueue('mails'));
        }

        return response()->json($import, 200);
    }
}
