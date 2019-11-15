<?php

namespace Modules\PatchRequests\Repositories;

use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Core\Models\Model;
use Modules\PatchRequests\Models\PatchRequest;
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

    /**
     * Filter patch request modifications by project_id and dlvry_type
     *
     * @param int $project_id
     * @param int $delivery_chain_type
     */
    public function getPRmodifications($project_id = null, $delivery_chain_type = null)
    {
        return DB::table('patch_requests as PR')
            ->join('issues as I', 'PR.issue_id', '=', 'I.id')
            ->leftJoin('modif_to_pr as mpr', 'PR.id', '=', 'mpr.pr_id')
            ->leftJoin('modifications as modif', 'mpr.modif_id', '=', 'modif.id')
            ->join('projects as PRJ', 'I.project_id', '=', 'PRJ.id')
            ->join('delivery_chains as DC', 'PR.delivery_chain_id', '=', 'DC.id')
            ->join('delivery_chain_types as DCT', 'DC.type_id', '=', 'DCT.id')
            ->join('v_current_pr_status as PRSTAT', 'PR.id', '=', 'PRSTAT.pr_id')
            ->join('enum_values as EV', 'PRSTAT.pr_status', '=', 'EV.id')
            ->select(
                'PR.id as pr_id',
                'PR.number as prnumber_id',
                'DC.title as dlvchain_title',
                'DC.id as dlvchain_id',
                'DCT.type AS delivery_chain_type',
                'EV.value as pr_tatus',
                'I.tts_id as tts_key',
                'modif.name as modification_name',
                'modif.version as version',
                'modif.prev_version as modif_prev_version',
                'modif.revision_converted as modif_revision_converted',
                'modif.type_id as modif_type_id',
                'modif.created_on as created_on'
            )
            ->where('EV.type', 'patch_requests_status_history_status')
            ->whereNotIn('EV.key', ['cancelled', 'rejected'])
            ->where('PRJ.id', $project_id)
            ->where('DCT.type', $delivery_chain_type)
            ->where(DB::raw('substr(I.created_on, 1, 4)'), '>=', '2016')
            ->whereNotIn('modif.type_id', ['oper', 'se', 'cmd'])
            ->orderBy('modif.name')
            ->orderByDesc('modif.created_on')
            ->orderByDesc('modif.revision_converted')
            ->get()
            ->toArray();
    }
}
