<?php

namespace Modules\DistributionGroups\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DistributionGroups\Repositories\DistributionGroupRepository;

class DistributionGroupController extends Controller
{

    public function __construct(DistributionGroupRepository $repository)
    {
        $this->repository = $repository;
    }
}