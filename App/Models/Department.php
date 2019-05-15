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
    public function departmentType()
    {
        return $this->belongsTo(EnumValue::class, 'department_type')->minimal();
    }
}
