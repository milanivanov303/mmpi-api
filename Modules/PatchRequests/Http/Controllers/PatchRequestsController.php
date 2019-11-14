<?php

namespace Modules\PatchRequests\Http\Controllers;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\PatchRequests\Repositories\PatchRequestRepository;

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
     * Get patch request modifications by project_id and dlvry_type
     *
     * @param int $project_id
     * @param string $delivery_chain_type
     * @return Response
     */
    public function getPRmodifications(int $project_id, string $delivery_chain_type)
    {
        return $this->repository->getPRmodifications($project_id, $delivery_chain_type);
    }
}
