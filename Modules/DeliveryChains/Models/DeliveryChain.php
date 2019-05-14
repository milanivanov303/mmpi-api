<?php

namespace Modules\DeliveryChains\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use Modules\Instances\Models\Instance;
use Modules\Projects\Models\Project;

class DeliveryChain extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'type',
        'dlvryType',
        'status',
        'dcVersion',
        'dcRole',
        'projects',
        'instances'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'type_id',
        'pivot'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'patch_directory_name'
    ];

    /**
     * Get type
     */
    public function type()
    {
        return $this->belongsTo(DeliveryChainType::class, 'type_id');
    }

    /**
     * Get dlvry_type
     */
    public function dlvryType()
    {
        return $this->belongsTo(EnumValue::class, 'dlvry_type');
    }

    /**
     * Get status
     */
    public function status()
    {
        return $this->belongsTo(EnumValue::class, 'status');
    }

    /**
     * Get dc_version
     */
    public function dcVersion()
    {
        return $this->belongsTo(EnumValue::class, 'dc_version');
    }

    /**
     * Get dc_role
     */
    public function dcRole()
    {
        return $this->belongsTo(EnumValue::class, 'dc_role');
    }

    /**
     * Get projects
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_to_delivery_chain')->minimal();
    }

    /**
     * Get instances
     */
    public function instances()
    {
        return $this->belongsToMany(Instance::class, 'instance_to_delivery_chain')->with('instanceType')->minimal();
    }

    /**
     * Get active delivery_chains
     */
    public function scopeActive($query)
    {
        return $query
                ->join('enum_values as status', 'status.id', '=', 'delivery_chains.status')
                ->where('status.key', '=', 'active');
    }
}
