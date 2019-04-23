<?php

namespace Modules\DeliveryChains\Models;

use Core\Models\Model;
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
        'status',
        'dcVersion',
        'dcRole'
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
}
