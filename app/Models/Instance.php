<?php

namespace App\Models;

use App\Modules\PatchRequests\Models\DeliveryChainType;

class Instance extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'owner',
        'type',
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
     * Get owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner')->with([
            'department',
            'accessGroup'
        ]);
    }

    /**
     * Get type
     */
    public function type()
    {
        return $this->belongsTo(InstanceType::class, 'instance_type_id');
    }

    public function environmentType()
    {
        return $this->belongsTo(DeliveryChainType::class, 'environment_type_id');
    }

    /**
     * Get type
     */
    public function status()
    {
        return $this->belongsTo(EnumValue::class, 'status')->minimal();
    }
}
