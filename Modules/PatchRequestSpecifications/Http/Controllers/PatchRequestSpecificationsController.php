<?php

namespace Modules\PatchRequestSpecifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PatchRequestSpecifications\Repositories\PatchRequestSpecificationRepository;

class PatchRequestSpecificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PatchRequestSpecificationRepository $repository
     * @return void
     */
    public function __construct(PatchRequestSpecificationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Update specifications
     *
     * @param int $patch_request_id
     * @param int $user_id
     */
    public function storePatchRequestSpecifications(Request $request, $patch_request_id, $user_id)
    {
        DB::transaction(function () use ($request, $patch_request_id, $user_id) {
            // Delete specifications for a given user and patch request
            DB::table('patch_requests_specifications')
                ->where('patch_request_id', '=', $patch_request_id)
                ->where('user_id', '=', $user_id)
                ->delete();

            $specifications = $request->all();

            $specificationsArr = [];
            // Get all modifications for given patch request
            foreach ($specifications['specification'] as $specification) {
                $specificationsArr[] = [
                    'patch_request_id' => $patch_request_id,
                    'user_id'          => $user_id,
                    'made_by'          => $user_id,
                    'made_on'          => $specifications['made_on'],
                    'specification'    => $specification
                ];
            }

            // Insert every modification as new record
            DB::table('patch_requests_specifications')->insert(
                $specificationsArr
            );
        });
    }

    /**
     * Delete specifications for a given user and patch request
     *
     * @param int $patch_request_id
     * @param int $user_id
     */
    public function deletePatchRequestSpecifications($patch_request_id, $user_id)
    {
        DB::transaction(function () use ($patch_request_id, $user_id) {
            DB::table('patch_requests_specifications')
                ->where('patch_request_id', '=', $patch_request_id)
                ->where('user_id', '=', $user_id)
                ->delete();
        });
    }
}
