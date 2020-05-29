<?php

namespace Modules\CurrentPatchStatus\Models;

use Core\Models\Model;

class CurrentPatchStatus extends Model
{
    protected $table = 'v_current_patch_status';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patch_id',
        'patch_status',
        'user',
        'date'
    ];
}
