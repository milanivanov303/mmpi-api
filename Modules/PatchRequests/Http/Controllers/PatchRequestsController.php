<?php

namespace Modules\PatchRequests\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\PatchRequests\Repositories\PatchRequestRepository;
use Modules\PatchRequests\Models\PatchRequestsSpecification;

class PatchRequestsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PatchRequestRepository $repository
     * @return void
     */
    public function __construct(PatchRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Insert/Update specifications
     * @param Request $request
     * @param int $patch_request_id
     * @param int $user_id
     */
    public function storePatchRequestSpecifications(Request $request, int $patch_request_id, int $user_id)
    {
        DB::transaction(function () use ($request, $patch_request_id, $user_id) {
            // Delete specifications for a given user and patch request
            $this->deletePatchRequestSpecifications($patch_request_id, $user_id);

            $specifications = $request->get('specifications');

            $made_on = Carbon::now()->format('Y-m-d H:i:s');

            $specificationsArr = [];
            // Get all modifications for given patch request
            foreach ($specifications as $specification) {
                $specificationsArr[] = [
                    'patch_request_id' => $patch_request_id,
                    'user_id'          => $user_id,
                    'made_by'          => Auth::user()->id,
                    'made_on'          => $made_on,
                    'specification'    => $specification
                ];
            }

            // Insert every modification as new record
            PatchRequestsSpecification::insert($specificationsArr);
        });
    }

    /**
     * Delete specifications for a given user and patch request
     *
     * @param int $patch_request_id
     * @param int $user_id
     */
    public function deletePatchRequestSpecifications(int $patch_request_id, int $user_id)
    {
        PatchRequestsSpecification::where('patch_request_id', '=', $patch_request_id)
            ->where('user_id', '=', $user_id)->delete();
    }
}
