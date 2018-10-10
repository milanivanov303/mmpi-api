<?php

namespace App\Models;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];
}
