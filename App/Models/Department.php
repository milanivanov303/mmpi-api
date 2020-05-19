<?php

namespace App\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use Modules\ProjectEvents\Models\ProjectEventEstimation;

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
     * Get id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get department by name
     *
     * @param string $name
     * @return User|null
     */
    public static function getByName(string $name) : ?self
    {
        return self::where('name', $name)->first();
    }

    /**
     * Get department type
     */
    protected function departmentType()
    {
        return $this->belongsTo(EnumValue::class, 'department_type');
    }

    /**
     * Get department type
     */
    protected function userDepartmentRoles()
    {
        return $this->hasMany(UserDepartmentRoles::class);
    }

    /**
     * Get project event estimations
     */
    protected function projectEventEstimations()
    {
        return $this->hasMany(ProjectEventEstimation::class);
    }

    /**
     * Get users
     */
    protected function users()
    {
        return $this->hasMany(User::class);
    }
}
