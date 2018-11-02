<?php

namespace App\Modules\DeliveryChains\Models;

use App\Models\Model;
use App\Models\EnumValue;

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
        'status'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'type_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'type_id',
        'dlvry_type',
        'status',
        'patch_directory_name',
        'dc_version',
        'dc_role'
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
        return $this->belongsTo(EnumValue::class, 'dlvry_type')->minimal();
    }

    /**
     * Get status
     */
    public function status()
    {
        return $this->belongsTo(EnumValue::class, 'status')->minimal();
    }
}
