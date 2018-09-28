<?php
namespace App\Traits;

/**
 * Trait to help mapping remote attributes to local names
 *
 * Define mapping with associative array.
 * ex. protected $mapping = ['name' => 'client_name'];
 *
 */
trait Mappable
{
    /**
     * Map attributes names
     *
     * @param array $attributes
     * @param array $mapping
     * @return array
     */
    protected function getMappededAttributes($attributes, $mapping)
    {
        foreach ($mapping as $from_key => $to_key) {
            if (array_key_exists($from_key, $attributes)) {
                $attributes[$to_key] = $attributes[$from_key];
                unset($attributes[$from_key]);
            }
        }
        return $attributes;
    }

    /**
     * Map attribute name
     *
     * @param string $attribute
     * @param array $mapping
     * @return string
     */
    protected function getMappededAttribute($attribute, $mapping)
    {
        if (array_key_exists($attribute, $mapping)) {
            return $mapping[$attribute];
        }
        return $attribute;
    }
    
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getMappededAttributes(parent::toArray(), $this->mapping);
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
            $this->getMappededAttributes($attributes, array_flip($this->mapping))
        );
    }
}
