<?php

namespace Modules\Modifications\Repositories;

use App\Models\EnumValue;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Issues\Models\Issue;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Modifications\Models\Modification;
use Modules\Modifications\Models\ModificationType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Instances\Models\Instance;
use App\Models\Department;
use App\Models\User;

class ModificationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectRepository constructor
     *
     * @param Modification $model
     */
    public function __construct(Modification $model)
    {
        $this->model = $model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Fill model attributes
     *
     * @param array $data
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);
        
        if (array_key_exists('action_type', $data)) {
            $this->model->actionType()->associate(
                app(EnumValue::class)
                    ->getModelId($data['action_type'], 'key', ['type' => 'src_action_type'])
            );
        }

        if (array_key_exists('delivery_chain', $data)) {
            $this->model->deliveryChain()->associate(
                app(DeliveryChain::class)->getModelId($data['delivery_chain'], ['id', 'title'])
            );
        }

        if (array_key_exists('creator_department', $data)) {
            $this->model->creatorDepartment()->associate(
                app(Department::class)->getModelId($data['creator_department'], 'id')
            );
        }

        if (array_key_exists('instance', $data)) {
            $this->model->instance()->associate(
                app(Instance::class)->getModelId($data['instance'], 'id')
            );
        }

        if (array_key_exists('instance_status', $data) && is_array($data['instance_status'])) {
            $this->model->instanceStatus()->associate(
                app(EnumValue::class)
                    ->getModelId($data['instance_status'], 'key', ['type' => 'instance_status'])
            );
        }

        if (array_key_exists('type', $data)) {
            $this->model->type()->associate(
                app(ModificationType::class)
                    ->getModelId($data['type'], 'id')
            );
        }
        
        if (array_key_exists('issue', $data)) {
            $this->model->issue()->associate(
                app(Issue::class)
                    ->getModelId($data['issue'], ['tts_id', 'jiraissue_id'])
            );
        }

        if (array_key_exists('path', $data)) {
            $this->model->path()->associate(
                app(EnumValue::class)
                    ->getModelId($data['path'], 'key', ['type' => 'source_paths'])
            );
        }

        if (array_key_exists('subtype', $data)) {
            $this->model->subtype()->associate(
                app(EnumValue::class)
                    ->getModelId($data['subtype'], 'key')
            );
        }

        if (array_key_exists('deployment_prefix', $data)) {
            $this->model->deploymentPrefix()->associate(
                app(EnumValue::class)
                    ->getModelId($data['deployment_prefix'], 'key')
            );
        }

        if ($this->model->exists) {
            $this->model->updatedBy()->associate(Auth::user());
            $this->model->updated_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            //fix to register modifications from apis as created by logged user
            array_key_exists('create_as_user', $data)
            && Auth::user()->username === config('app.user-management.username')
                ? $this->model->createdBy()->associate(
                    app(User::class)->getModelId($data['create_as_user'], ['id', 'username'])
                )
                : $this->model->createdBy()->associate(Auth::user());

            $this->model->created_on = Carbon::now()->format('Y-m-d H:i:s');
        }

        $this->model->creator_department_id = Auth::user()->department->id;
    }

    /**
     * Filter modifications by project_id and dlvry_type
     *
     * @param int $project_id
     * @param int $delivery_chain_type
     */
    public function getByProjectAndChainType($project_id = null, $delivery_chain_type = null)
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
                'modif.checksum as checksum',
                'modif.prev_version as modif_prev_version',
                'modif.revision_converted as modif_revision_converted',
                'modif.type_id as modif_type_id',
                'modif.created_on  as created_on'
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
