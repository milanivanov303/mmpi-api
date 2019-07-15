<?php

namespace Modules\Branches\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use App\Models\User;

class Branch extends Model
{
    /**
     * Set the table associated with the model.
     *
     * @var array
     */
    protected $table = "hash_branches";

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'repo_type_id',
        'description',
        'created_at',
        'made_by',
        'repo_master_branch'
    ];

    /**
     * Get repository type
     */
    protected function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id');
    }

    /**
     * Get users
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
