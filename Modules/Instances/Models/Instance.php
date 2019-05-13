<?php

namespace Modules\Instances\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChainType;

class Instance extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'owner',
        'instanceType',
        'status',
        'environmentType'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'instance_type_id',
        'environment_type_id'
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
        return $this->belongsTo(EnumValue::class, 'owner')->minimal();
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
        return $this->belongsTo(EnumValue::class, 'status')->minimal();
    }
}
