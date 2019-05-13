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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'repoType',
        'madeBy'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'repo_type_id'
    ];

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
    public function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id')->minimal();
    }

    /**
     * Get users
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by')->minimal();
    }
}
