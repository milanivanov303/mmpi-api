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
     * Map request to original model attributes
     *
     * @var integer
     */
    public static $MAP_REQUEST_VALUES = 1;

    /**
     * Map model to original request attributes
     *
     * @var integer
     */
    public static $MAP_RESPONSE_VALUES = 2;

    /**
     * Map attributes names
     *
     * @param array $attributes
     * @param integer $flag
     * @return array
     */
    protected function getMappededAttributes($attributes, $flag = null)
    {
        $mapping = $this->getMapping($flag);

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
    public function getMappededAttribute($attribute, $flag = null)
    {
        $mapping = $this->getMapping($flag);

        if (array_key_exists($attribute, $mapping)) {
            return $mapping[$attribute];
        }
        return $attribute;
    }

    /**
     * Get mapping depending on the flag set
     *
     * @param integer $flag
     * @return array
     */
    protected function getMapping($flag)
    {
        return (is_null($flag) || $flag === $this::$MAP_REQUEST_VALUES) ? array_flip($this->mapping) : $this->mapping;
    }
    
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getMappededAttributes(parent::toArray(), $this::$MAP_RESPONSE_VALUES);
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
            $this->getMappededAttributes($attributes)
        );
    }
}
