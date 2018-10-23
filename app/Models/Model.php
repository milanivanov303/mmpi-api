<?php

namespace App\Models;

use App\Helpers\DataMapper;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    /**
     * @var DataMapper
     */
    public $mapper;

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mapper = new DataMapper($this->mapping);
        parent::__construct($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        return parent::fill(
            $this->mapper->mapRequestData($attributes)
        );
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->mapper->mapResponseData(parent::toArray());
    }

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

    /**
     * Set the visible attributes for the model.
     *
     * @param  array  $visible
     * @return $this
     */
    public function setVisible(array $visible)
    {
        // We need to map items to local names and keep both, because relations will stop working
        $visible = array_unique(
            array_merge(
                $visible,
                array_flip(
                    $this->mapper->mapRequestData(array_flip($visible))
                )
            )
        );

        return parent::setVisible($visible);
    }
}
