<?php

namespace App\Models;

class Dependency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rev_id',
        'rev_type_id',
        'dep_id',
        'dep_type_id',
        'functional',
        'comment',
        'added_by',
        'scope',
        'deleted'
    ];
}
