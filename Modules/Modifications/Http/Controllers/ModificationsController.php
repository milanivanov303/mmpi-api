<?php

namespace Modules\Modifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Modifications\Repositories\ModificationRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Http\Resources\ResourceModel;
use Core\Http\Resources\ResourceCollection;
use Modules\Modifications\Http\Resources\SourceResourceModel;

class ModificationsController extends Controller
{
    protected $type;

    /**
     * Create a new controller instance.
     *
     * @param ModificationRepository $model
     * @return void
     */
    public function __construct(ModificationRepository $model, Request $request)
    {
        $this->model = $model;
        $this->type  = $request->route()[1]['type'] ?? null;

        // set type_id in request
        if ($this->type) {
            $request->json()->add(['type_id' => $this->type]);
        }
    }

    /**
     * Get output response
     *
     * @param mixed $data
     *
     * @return JsonResource
     */
    public function output($data)
    {
        if ($data instanceof \Core\Models\Model) {
            if ($this->type === 'source') {
                return new SourceResourceModel($data);
            }

            return new ResourceModel($data);
        }

        return new ResourceCollection($data);
    }
}
