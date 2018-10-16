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

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);

        // set model visible attributes from original model
        $model->setVisible($this->getVisible());

        return $model;
    }

    /**
     * Check if attribute is set as visible
     * 
     * @param string $attribute
     * @return bool
     */
    public function isVisible($attribute)
    {
        return empty($this->getVisible()) || in_array($attribute, $this->getVisible());
    }

}
