<?php

namespace App\Models;

class AccessGroup extends Model
{
    /**
     * Get access group by name
     *
     * @param string $name
     * @return User|null
     */
    public static function getByName(string $name) : ?self
    {
        return self::where('name', $name)->first();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() : int
    {
        return $this->attributes['id'];
    }
}
