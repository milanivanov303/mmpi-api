<?php

namespace App\Models;

class EnumValue extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'changed_by'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'subtype',
        'key',
        'value',
        'description',
        'url',
        'active',
        'sortindex',
        'extra_property',
        'changed_on',
        'changed_by'
    ];
    
    /**
     * Get changedBy
     */
    protected function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
