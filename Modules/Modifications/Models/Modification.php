<?php

namespace Modules\Modifications\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Modules\Issues\Models\Issue;
use Modules\Instances\Models\Instance;
use Modules\DeliveryChains\Models\DeliveryChain;
use App\Models\User;
use App\Models\EnumValue;
use App\Models\DbSchema;
use App\Models\Department;
use Modules\PatchRequests\Models\PatchRequest;

class Modification extends Model
{
    /**
     * Modification type
     *
     * @var string|null
     */
    protected static $type = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modifications';

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'namesake',
        'run_repack',
        'prev_version',
        'version',
        'revision_converted',
        'contents',
        'comments',
        'checksum',
        'size',
        'est_run_time',
        'permissions',
        'backup_orig_data',
        'backup_type',
        'backup_where_clause',
        'trig_status',
        'seq_table_name',
        'seq_column_name',
        'header_only',
        'title',
        'maven_repository',
        'deployment_path',
        'check_exit_status',
        'target_schema',
        'check_status',
        'check_msg',
        'checked_on',
        'active',
        'visible',
        'locked',
        'is_buggy',
        'marked_buggy_by',
        'marked_buggy_on',
        'bad_content_confirmed',
        'branch',
        'old_id',
        'issue_id',
        'delivery_chain_id',
        'locked_by_id',
        'copied_from_user_id',
        'instance_id',
        'deployment_prefix_id',
        'path_id',
        'subtype_id',
        'tablespace_id',
        'created_by_id',
        'creator_department_id',
        'updated_by_id',
        'type_id',
        'created_on',
        'instance_status'
    ];

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        if (static::$type) {
            static::addGlobalScope('type', function (Builder $builder) {
                $builder->where('type_id', static::$type);
            });
        }
    }

    /**
     * Get issue
     */
    protected function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get created by
     */
    protected function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get locked by
     */
    protected function lockedBy()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get marked buggy by
     */
    protected function markedBuggyBy()
    {
        return $this->belongsTo(User::class, 'marked_buggy_by');
    }

    /**
     * Get updated by
     */
    protected function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * Get delivery chain
     */
    protected function deliveryChain()
    {
        return $this->belongsTo(DeliveryChain::class);
    }

    /**
     * Get copied by user
     */
    protected function copiedFromUser()
    {
        return $this->belongsTo(User::class, 'copied_from_user_id');
    }

    /**
     * Get instance
     */
    protected function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get action type
     */
    protected function actionType()
    {
        return $this->belongsTo(EnumValue::class, 'action_type');
    }

    /**
     * Get backup type
     */
    protected function backupType()
    {
        return $this->belongsTo(EnumValue::class, 'backup_type');
    }

    /**
     * Get check msg
     */
    protected function checkMsg()
    {
        return $this->belongsTo(EnumValue::class, 'check_msg');
    }

    /**
     * Get check status
     */
    protected function checkStatus()
    {
        return $this->belongsTo(EnumValue::class, 'check_status');
    }

    /**
     * Get deployment prefix
     */
    protected function deploymentPrefix()
    {
        return $this->belongsTo(EnumValue::class, 'deployment_prefix_id');
    }

    /**
     * Get instance status
     */
    protected function instanceStatus()
    {
        return $this->belongsTo(EnumValue::class, 'instance_status');
    }

    /**
     * Get path
     */
    protected function path()
    {
        return $this->belongsTo(EnumValue::class, 'path_id');
    }

    /**
     * Get target schema
     */
    protected function targetSchema()
    {
        return $this->belongsTo(DbSchema::class, 'target_schema');
    }


    /**
     * Get subtype
     */
    protected function subtype()
    {
        return $this->belongsTo(EnumValue::class, 'subtype_id');
    }

    /**
    * Get tablespace
    */
    protected function tablespace()
    {
        return $this->belongsTo(EnumValue::class, 'tablespace_id');
    }

    /**
    * Get trig status
    */
    protected function trigStatus()
    {
        return $this->belongsTo(EnumValue::class, 'trig_status');
    }

    /**
    * Get type
    */
    protected function type()
    {
        return $this->belongsTo(ModificationType::class, 'type_id');
    }

    /**
     * Get department of modification's creator
     */
    protected function creatorDepartment()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get patch request if modification is included in one
     */
    protected function patchRequest()
    {
        return $this->belongsToMany(PatchRequest::class, 'modif_to_pr', 'modif_id', 'pr_id')
            ->wherePivot('removed', null);
    }
}
