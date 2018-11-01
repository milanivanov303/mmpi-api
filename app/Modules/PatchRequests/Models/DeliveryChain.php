<?php

namespace App\Modules\PatchRequests\Models;

use App\Models\Model;

class DeliveryChain extends Model
{

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

    public function type()
    {
        return $this->belongsTo(DeliveryChainType::class, 'type_id');
    }
}
