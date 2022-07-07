<?php

namespace Modules\Instances\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Instances\Repositories\InstanceRepository;
use Modules\Hr\Services\HrService;
use Illuminate\Http\JsonResponse;
use Modules\Projects\Repositories\ProjectRepository;

class InstancesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstanceRepository $repository
     * @return void
     */
    public function __construct(InstanceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function availablePmoStaff(
        string $instance,
        HrService $hrService,
        ProjectRepository $projectRepository
    ) :JsonResponse {
        try {
            $project = $projectRepository->findByInstanceName($instance);
            $availablePmoStaff = $hrService->getProjectAvailablePmo($project['name']);
            return response()->json(['data'=> $availablePmoStaff]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 400);
        }
    }
}
