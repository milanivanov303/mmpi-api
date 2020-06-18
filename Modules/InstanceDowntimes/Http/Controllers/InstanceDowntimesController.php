<?php

namespace Modules\InstanceDowntimes\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\InstanceDowntimes\Repositories\InstanceDowntimeRepository;
use Illuminate\Http\Request;
use Modules\InstanceDowntimes\Models\InstanceDowntime;
use Modules\InstanceDowntimes\Services\NotificationService;

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
     * Update unix access request
     *
     * @param Request $request
     * @param mixed ...$id
     * @return Response
     */
    public function update(Request $request, ...$id)
    {
        $data  = $request->all();
        $model = InstanceDowntime::with(['instance.deliveryChains.projects'])
            ->findOrFail((int)$id[0]);

        if ($model->count() === 0) {
            return;
        }

        app(NotificationService::class, [
            "model" => $model,
            "data" => $data
        ])->sendNotification();

        return parent::update($request, ...$id);
    }
}
