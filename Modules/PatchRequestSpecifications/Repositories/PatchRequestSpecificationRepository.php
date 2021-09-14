<?php

namespace Modules\PatchRequestSpecifications\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\PatchRequestSpecifications\Models\PatchRequestsSpecification;
use Modules\PatchRequests\Models\PatchRequest;
use function Symfony\Component\String\s;
use Illuminate\Support\Facades\DB;

class PatchRequestSpecificationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectSpecificRepository constructor
     *
     * @param PatchRequestsSpecification $model
     */
    public function __construct(PatchRequestsSpecification $model)
    {
        $this->model = $model;
    }


    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        $this->model->user()->associate(Auth::user());

        $this->model->madeBy()->associate(Auth::user());

        $this->model->made_on = Carbon::now()->format('Y-m-d H:i:s');
    }

    /**
     * Save record
     *
     * @param array $data
     * @return Model
     *
     * @throws \Throwable
     */
    protected function save($data)
    {
        if (array_key_exists('patch_request_id', $data)) {
            $specifications = [];
            foreach ($data['specification'] as $specification) {
                $specifications[] = [
                    'patch_request_id' => $data['patch_request_id'],
                    'user_id'          => $data['user_id'],
                    'made_by'          => $data['made_by'],
                    'made_on'          => $data['made_on'],
                    'specification'    => $specification
                ];
            }
            foreach ($specifications as $spec) {
                $model = $this->model->newInstance();
                $model->fill($spec);
                $model->save();
            }
        }

        $this->loadModelRelations($data);

        return $this->model;
    }

    /**
     * Delete record
     *
     * @param mixed $id
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $this->model->newModelQuery()->where('patch_request_id', '=', $id)->delete();
        });
    }
}
