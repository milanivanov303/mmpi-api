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
        'pivot',
        'owner',
        'status'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'play_as_demo',
        'timezone',
        'host',
        'user',
        'db_user',
        'tns_name',
        'has_patch_install_in_init',
        'owner',
        'status'
    ];

    /**
     * Get owner
     */
    protected function owner()
    {
        return $this->belongsTo(EnumValue::class, 'owner');
    }

    /**
     * Get instance type
     */
    protected function instanceType()
    {
        return $this->belongsTo(InstanceType::class);
    }

    /**
     * Get environment type
     */
    protected function environmentType()
    {
        return $this->belongsTo(DeliveryChainType::class);
    }

    /**
     * Get status
     */
    protected function status()
    {
        return $this->belongsTo(EnumValue::class, 'status');
    }

    /**
     * Get delivery_chains
     */
    protected function deliveryChains()
    {
        return $this->belongsToMany(DeliveryChain::class, 'instance_to_delivery_chain')->active();
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
}
