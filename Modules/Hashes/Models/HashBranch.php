<?php

namespace Modules\Hashes\Models;

use App\Models\EnumValue;
use App\Models\User;
use Core\Models\Model;

class HashBranch extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        //'repoType',
        //'madeBy'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'repo_type_id',
        'made_by'
    ];

    /**
     * Get user for the hash.
     */
    public function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id');
    }

    /**
     * Get user for the hash.
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
