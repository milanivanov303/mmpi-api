<?php

namespace App\Modules\PatchRequests\Models;

use App\Models\Model;

class DeliveryChain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'type_id',
        'dlvry_type',
        'status',
        'patch_directory_name',
        'dc_version',
        'dc_role'
    ];
}
