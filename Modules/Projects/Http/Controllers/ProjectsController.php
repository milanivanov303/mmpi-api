<?php

namespace Modules\Projects\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProjectRole;
use App\Models\UserProjectRoleTmp;
use Modules\Projects\Repositories\ProjectRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Modules\Projects\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Hr\Services\HrService;
use Modules\Projects\Mail\ProjectRolesChangeMail;
use Modules\Projects\Exports\ProjectsExport;

class ProjectsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectRepository $repository
     * @return void
     */
    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Update temporary roles
     *
     * @return JsonResource
     */
    public function updateRolesTmp(Request $request, int $id)
    {
        try {
            $roles = $request->all();
            DB::transaction(function () use (&$id, &$roles) {
                UserProjectRoleTmp::where('project_id', $id)->delete();

                UserProjectRoleTmp::insert(
                    array_map(function ($role) use ($id) {
                        return array_merge($role, [
                            'project_id' => $id,
                            'made_on'    => Carbon::now()->format('Y-m-d H:i:s'),
                            'made_by'    => Auth::user()->id
                            ]);
                    }, $roles)
                );
            });

            $project = Project::with('roles.user', 'rolesTmp.user')->find($id);

            // uncomment when the recipient will be the Auth user on DEV
            // $message = new ProjectRolesChangeMail(
            //     $project->name,
            //     Auth::user()->name,
            //     config('app.dev-management-url') . '/pmo/organization'
            // );
            // Mail::to(config('app.pmo-management-mails'))
            //     ->cc(Auth::user()->email)
            //     ->queue($message->onQueue('mails'));

            return response()->json(['data'=> $project]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 400);
        }
    }

    /**
     * Update roles
     *
     * @return JsonResource
     */
    public function updateRoles(Request $request, int $id)
    {
        try {
            $roles = $request->all();

            DB::transaction(function () use (&$id, &$roles) {
                UserProjectRoleTmp::where('project_id', $id)->delete();
                UserProjectRole::where('project_id', $id)->delete();

                UserProjectRole::insert(
                    array_map(function ($role) use ($id) {
                        return array_merge($role, [
                            'project_id' => $id,
                            'made_on'    => Carbon::now()->format('Y-m-d H:i:s'),
                            'made_by'    => Auth::user()->id
                            ]);
                    }, $roles)
                );
            });

            $project = Project::with('roles.user', 'rolesTmp.user')->find($id);
            return response()->json(['data'=> $project]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 400);
        }
    }

    /**
     * Export and return file
     *
     * @return void
     */
    public function export(string $type)
    {
        if ($type === 'wiki') {
            return (new ProjectsExport)->download('projects.xlsx');
        }
    }

    public function availablePmoStaff(string $project, HrService $hrService) :JsonResponse
    {
        try {
            $availablePmoStaff = $hrService->getProjectAvailablePmo($project);
            return response()->json(['data'=> $availablePmoStaff]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 400);
        }
    }
}
