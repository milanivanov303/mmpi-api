<?php
namespace App\Helpers;

/**
 * Class to help mapping remote attributes to local names
 *
 * Define mapping with associative array.
 * ex. protected $mapping = ['name' => 'client_name'];
 *
 */
class DataMapper
{
    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping;

    /**
     * Mapper constructor
     *
     * @param array $mapping
     */
    public function __construct(array $mapping = [])
    {
        $this->mapping = $mapping;
    }

    /**
     * Get mapped data for response
     *
     * @param array $data
     * @return array
     */
    public function mapResponseData(array $data) : array
    {
        return $this->getMappedAttributes(
            $data,
            $this->mapping
        );
    }

    /**
 * Map received request data
 *
 * @param array $data
 * @return array
 */
    public function mapRequestData(array $data) : array
    {
        return $this->getMappedAttributes(
            $data,
            array_flip($this->mapping)
        );
    }

    /**
     * Map attributes names
     *
     * @param array $attributes
     * @param array $mapping
     * @return array
     */
    protected function getMappedAttributes(array $attributes, array $mapping) : array
    {
        // TODO: refactor this to keep array order
        foreach ($mapping as $from_key => $to_key) {
            if (array_key_exists($from_key, $attributes)) {
                $attributes[$to_key] = $attributes[$from_key];
                unset($attributes[$from_key]);
            }
        }
        return $attributes;
    }
}
