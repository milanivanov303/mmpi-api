<?php

namespace Modules\DeliveryChains\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use Modules\Branches\Models\Branch;
use Modules\Instances\Models\Instance;
use Modules\Projects\Models\Project;

class DeliveryChain extends Model
{
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
        'title',
        'patch_directory_name',
        'dlvry_type',
        'status',
        'dc_version',
        'dc_role',
        'type_id'
    ];

    /**
     * Get type
     */
    protected function type()
    {
        return $this->belongsTo(DeliveryChainType::class, 'type_id');
    }

    /**
     * Get dlvry_type
     */
    protected function dlvryType()
    {
        return $this->belongsTo(EnumValue::class, 'dlvry_type');
    }

    /**
     * Get status
     */
    protected function status()
    {
        return $this->belongsTo(EnumValue::class, 'status');
    }

    /**
     * Get dc_version
     */
    protected function dcVersion()
    {
        return $this->belongsTo(EnumValue::class, 'dc_version');
    }

    /**
     * Get dc_role
     */
    protected function dcRole()
    {
        return $this->belongsTo(EnumValue::class, 'dc_role');
    }

    /**
     * Get projects
     */
    protected function projects()
    {
        return $this->belongsToMany(Project::class, 'project_to_delivery_chain')->active();
    }

    /**
     * Get instances
     */
    protected function instances()
    {
        return $this->belongsToMany(Instance::class, 'instance_to_delivery_chain')
                    ->active()
                    ->as('instance_to_delivery_chain')
                    ->withPivot('instance_previous_id');
    }

    /**
     * Get branches
     */
    protected function branches()
    {
        return $this->belongsToMany(Branch::class, 'delivery_chain_branch', null, 'repo_branch_id');
    }

    /**
     * Get active delivery_chains
     */
    public function scopeActive($query)
    {
        return $query->whereHas('status', function ($q) {
                    $q->where('key', 'active');
        });
    }
     
    public function getDcVersionAttribute($value)
    {
        return (int)$value;
    }
    
    public function getDcRoleAttribute($value)
    {
        return (int)$value;
    }
}
