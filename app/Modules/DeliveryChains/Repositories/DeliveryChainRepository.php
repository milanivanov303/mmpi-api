<?php

namespace App\Modules\DeliveryChains\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Modules\DeliveryChains\Models\DeliveryChain;

class DeliveryChainRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'title';

    /**
     * ProjectRepository constructor
     *
     * @param DeliveryChain $model
     */
    public function __construct(DeliveryChain $model)
    {
        $this->model = $model;
    }

    /**
     * Save record
     *
     * @param array $data
     * @return Model
     */
    protected function save($data)
    {
        $this->model->type()->associate($data['type']['id']);
        $this->model->dlvryType()->associate($data['dlvry_type']['id']);

        // seting associate on relations with same name as column fails!!!
        //$this->model->status()->associate($data['status']['id']);
        $this->model->status = $data['status']['id'];

        $this->model->dcVersion()->associate(isset($data['dc_version']) ? $data['dc_version']['id'] : null);
        $this->model->dcRole()->associate(isset($data['dc_role']) ? $data['dc_role']['id'] : null);

        $this->model->fill($data)->saveOrFail();
        $this->model->load($this->getWith());

        return $this->model;
    }
}
