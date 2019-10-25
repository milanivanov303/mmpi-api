<?php

namespace Modules\PatchRequests\Repositories;

use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Core\Models\Model;
use Modules\PatchRequests\Models\PatchRequest;
use Modules\Issues\Models\Issue;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Modifications\Models\Modification;
use Illuminate\Support\Facades\DB;

class PatchRequestRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * PatchRequestRepository constructor
     *
     * @param PatchRequest $model
     */
    public function __construct(PatchRequest $model)
    {
        $this->model = $model;
    }

    /**
     * Save patch request
     *
     * @param array $data
     * @return Model
     */
    protected function save($data)
    {
        $this->model->fill($data);

        DB::transaction(function () use ($data) {
            $this->model->issue()->associate($data['issue']['id']);
            $this->model->deliveryChain()->associate($data['delivery_chain']['id']);

            $this->model->saveOrFail();

            $this->syncModifications($data['modifications'] ?? []);
        });

        $this->loadModelRelations($data);

        return $this->model;
    }

    /**
     * Sync modifications
     * TODO: refactor this method when I have clear idea what should it do
     *
     * @param array $modifications
     */
    protected function syncModifications($modifications)
    {
        // eloquent sync method is not working because pivot table is too custom!!!
        //$model->modifications()->sync(array_column($modifications, 'id'));

        // create new array using id as keys and type_id as values
        $modifications = array_combine(
            array_column($modifications, 'id'),
            array_column($modifications, 'type_id')
        );

        // Get old modifications ids
        $oldModifications = array_column(
            DB::table('modif_to_pr')
                ->select('modif_id')
                ->where('pr_id', $this->model->id)
                ->get()
                ->toArray(),
            'modif_id'
        );

        // Get new modifications ids
        $newModifications = array_keys($modifications);

        // Detach removed modifications
        $removed = array_diff($oldModifications, $newModifications);
        DB::table('modif_to_pr')
            ->where('pr_id', $this->model->id)
            ->whereIn('modif_id', $removed)
            ->update(['removed' => 1]);

        // Reattach modifications
        $updated = array_intersect($oldModifications, $newModifications);
        DB::table('modif_to_pr')
            ->where('pr_id', $this->model->id)
            ->whereIn('modif_id', $updated)
            ->update(['removed' => null]);

        // Attach new modifications
        $added = array_diff($newModifications, $oldModifications);
        $order = count($oldModifications);
        DB::table('modif_to_pr')->insert(
            array_map(function ($modif_id) use ($modifications, &$order) {
                $type_id = $modifications[$modif_id];
                return [
                    'pr_id'    => $this->model->id,
                    'modif_id' => $modif_id,
                    'type_id'  => $modifications[$modif_id],
                    'order'    => $order++
                ];
            }, $added)
        );
    }
}
