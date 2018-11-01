<?php

namespace App\Models;

class Department extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'departmentType'
    ];

    /**
     * Get departmentType
     */
    public function departmentType()
    {
        return $this->belongsTo(EnumValue::class, 'department_type')->minimal();
    }
}
