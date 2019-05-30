<?php

namespace Modules\DeliveryChains\Models;

use Core\Models\Model;
use App\Models\EnumValue;
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
        'type_id',
        'dlvry_type',
        'status',
        'dc_version',
        'dc_role',
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
        'dc_role'
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
        return $this->belongsToMany(Project::class, 'project_to_delivery_chain');
    }

    /**
     * Get instances
     */
    protected function instances()
    {
        return $this->belongsToMany(Instance::class, 'instance_to_delivery_chain');
    }

    /**
     * Get active delivery_chains
     */
    public function scopeActive($query)
    {
        return $query
        ->select([
            'delivery_chains.id',
            'delivery_chains.title',
            'dlvry_type',
            'patch_directory_name',
            'dc_version',
            'dc_role',
            'type_id',
            'status'])
            ->join('enum_values as etype', 'etype.id', '=', 'delivery_chains.type_id')
            ->join('enum_values as status', 'status.id', '=', 'delivery_chains.status')
            ->where('status.key', '=', 'active');
    }
}
