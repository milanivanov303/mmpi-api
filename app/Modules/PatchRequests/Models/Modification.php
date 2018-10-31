<?php

namespace App\Modules\PatchRequests\Models;

use App\Models\Model;
use App\Modules\Issues\Models\Issue;
use App\Models\User;

class Modification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'issue_id',
        'pivot',
        'delivery_chain_id',
        'created_by_id'
    ];

    /**
     * Get issue
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class)->with(['project', 'devInstance']);
    }

    /**
     * Get created by
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
