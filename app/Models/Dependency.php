<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependency extends Model
{
    public $timestamps = false;
    
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
        'added_on',
        'scope',
        'deleted'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
