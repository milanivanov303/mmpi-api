<?php

namespace Modules\InstanceDowntimes\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\InstanceDowntimes\Repositories\InstanceDowntimeRepository;
use Illuminate\Http\Request;
use Modules\InstanceDowntimes\Models\InstanceDowntime;

class InstanceDowntimesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InstanceDowntimeRepository $repository
     * @return void
     */
    public function __construct(InstanceDowntimeRepository $repository)
    {
        $this->repository = $repository;
    }

   /**
     * @inheritDoc
     */
    public function update(Request $request, ...$id)
    {
        //$model = InstanceDowntime::with(['instance.deliveryChains.projects'])
        //    ->findOrFail((int)$id[0]);

        // app(NotificationService::class, [
        //     "model" => $model,
        //     "data" => $request->all()
        // ])->sendNotification();

        return parent::update($request, ...$id);
    }
}
