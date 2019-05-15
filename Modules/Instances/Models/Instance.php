<?php

namespace Modules\Instances\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChainType;
use Modules\DeliveryChains\Models\DeliveryChain;

class Instance extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'instance_type_id',
        'environment_type_id',
        'pivot'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'play_as_demo',
        'owner',
        'status',
        'timezone',
        'host',
        'user',
        'db_user',
        'tns_name',
        'has_patch_install_in_init',
        'instance_type_id',
        'environment_type_id'
    ];

    /**
     * Get owner
     */
    public function owner()
    {
        return $this->belongsTo(EnumValue::class, 'owner');
    }

    /**
     * Get instance type
     */
    public function instanceType()
    {
        return $this->belongsTo(InstanceType::class);
    }

    /**
     * Get environment type
     */
    public function environmentType()
    {
        return $this->belongsTo(DeliveryChainType::class);
    }

    /**
     * Get status
     */
    public function status()
    {
        return $this->belongsTo(EnumValue::class, 'status');
    }

    /**
     * Get delivery_chains
     */
    public function deliveryChains()
    {
        return $this->belongsToMany(DeliveryChain::class, 'instance_to_delivery_chain')->without('instances')->active();
    }

    /**
     * Get active instances with specific properties
     */
    public function scopeMinimal($query)
    {
        return $query
                ->select(['instances.id', 'name', 'instance_type_id'])
                ->join('enum_values as status', 'status.id', '=', 'instances.status')
                ->where('status.key', '=', 'active');
    }
}
