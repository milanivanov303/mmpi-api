<?php

namespace Modules\DeliveryChains\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DeliveryChains\Repositories\DeliveryChainRepository;

class DeliveryChainsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DeliveryChainRepository $model
     * @return void
     */
    public function __construct(DeliveryChainRepository $model)
    {
        $this->model = $model;
    }
}
