<?php

namespace Modules\DeliveryChainTypes\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DeliveryChainTypes\Repositories\DeliveryChainTypeRepository;

class DeliveryChainTypesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DeliveryChainTypeRepository $repository
     * @return void
     */
    public function __construct(DeliveryChainTypeRepository $repository)
    {
        $this->repository = $repository;
    }
}
