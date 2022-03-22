<?php

namespace Modules\Sources\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Sources\Repositories\SourceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sources\Models\Source;

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

    /**
     * Search in sorces by file
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request) : JsonResponse
    {
        if (!$request->input('sources')) {
            return new JsonResponse(["error" => "No valid sources list is provided."], 422);
        }

        try {
            $sources = $request->input('sources');
    
            $result = Source::whereIn('source_name', $sources)
                ->with(['department', 'departmentAssignedBy'])
                ->get();

            return new JsonResponse(["data" => $result->all()], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e], 400);
        }
    }
}
