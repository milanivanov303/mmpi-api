<?php

namespace App\Models;

class Department extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'department_type'
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
     * Get departmentType
     */
    protected function departmentType()
    {
        return $this->belongsTo(EnumValue::class, 'department_type');
    }
}
