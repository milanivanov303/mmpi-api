<?php

namespace Modules\Departments\Models;

use Core\Models\Model;
use App\Models\EnumValue;

class Department extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hr_department_id',
        'name',
        'default_access_group_id',
        'src_dlv_by_revision',
        'department_type',
        'status'
    ];

    /**
     * Get default access group
     */
    protected function departmentType()
    {
        return $this->belongsTo(EnumValue::class, 'department_type');
    }
}
