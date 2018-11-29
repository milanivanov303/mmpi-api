<?php

namespace Modules\Modifications\Models;

use App\Models\Model;
use Modules\Issues\Models\Issue;
use Modules\Instances\Models\Instance;
use Modules\DeliveryChains\Models\DeliveryChain;
use App\Models\User;
use App\Models\EnumValue;
use App\Models\DbSchema;

class Modification extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'issue',
        'createdBy',
        'lockedBy',
        'markedBuggyBy',
        'deliveryChain',
        'copiedByUser',
        'instance',
        'actionType',
        'backupType',
        'checkMsg',
        'checkStatus',
        'deploymentPrefix',
        'instanceStatus',
        'path',
        'targetSchema',
        'subtype',
        'tablespace',
        'updatedBy',
        'trigStatus',
        'type'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'old_id',
        'issue_id',
        'pivot',
        'delivery_chain_id',
        'created_by_id',
        'locked_by_id',
        'copied_by_user_id',
        'instance_id',
        'deployment_prefix_id',
        'path_id',
        'subtype_id',
        'tablespace_id',
        'updated_by_id',
        'type_id'
    ];

    /**
     * Get issue
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class)->with(['project', 'devInstance']);
    }

    /**
     * Get created by
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get locked by
     */
    public function lockedBy()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get marked buggy by
     */
    public function markedBuggyBy()
    {
        return $this->belongsTo(User::class, 'marked_buggy_by');
    }

    /**
     * Get updated by
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * Get delivery chain
     */
    public function deliveryChain()
    {
        return $this->belongsTo(DeliveryChain::class);
    }

    /**
     * Get copied by user
     */
    public function copiedByUser()
    {
        return $this->belongsTo(User::class, 'copied_by_user_id');
    }

    /**
     * Get instance
     */
    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get action type
     */
    public function actionType()
    {
        return $this->belongsTo(EnumValue::class, 'action_type')->minimal();
    }

    /**
     * Get backup type
     */
    public function backupType()
    {
        return $this->belongsTo(EnumValue::class, 'backup_type')->minimal();
    }

    /**
     * Get check msg
     */
    public function checkMsg()
    {
        return $this->belongsTo(EnumValue::class, 'check_msg')->minimal();
    }

    /**
     * Get check status
     */
    public function checkStatus()
    {
        return $this->belongsTo(EnumValue::class, 'check_status')->minimal();
    }

    /**
     * Get deployment prefix
     */
    public function deploymentPrefix()
    {
        return $this->belongsTo(EnumValue::class, 'deployment_prefix_id')->minimal();
    }

    /**
     * Get instance status
     */
    public function instanceStatus()
    {
        return $this->belongsTo(EnumValue::class, 'instance_status')->minimal();
    }

    /**
     * Get path
     */
    public function path()
    {
        return $this->belongsTo(EnumValue::class, 'path_id')->minimal();
    }

    /**
     * Get target schema
     */
    public function targetSchema()
    {
        return $this->belongsTo(DbSchema::class, 'target_schema');
    }


    /**
     * Get subtype
     */
    public function subtype()
    {
        return $this->belongsTo(EnumValue::class, 'subtype_id')->minimal();
    }

    /**
    * Get tablespace
    */
    public function tablespace()
    {
        return $this->belongsTo(EnumValue::class, 'tablespace_id')->minimal();
    }

    /**
    * Get trig status
    */
    public function trigStatus()
    {
        return $this->belongsTo(EnumValue::class, 'trig_status')->minimal();
    }

    /**
    * Get type
    */
    public function type()
    {
        return $this->belongsTo(ModificationType::class, 'type_id');
    }
}
