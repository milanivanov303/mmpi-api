<?php

namespace Modules\Instances\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChainType;

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
}
