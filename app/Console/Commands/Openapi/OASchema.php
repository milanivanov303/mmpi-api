<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Contracts\Support\Arrayable;

class OASchema implements Arrayable
{
    /**
     * Schema id
     *
     * @var string
     */
    protected $id;

    /**
     * Schema data
     *
     * @var array
     */
    protected $data;

    /**
     * Not valid OpenApi properties
     */
    const NOT_VALID_PROPERTIES = [
        '$id',
        '$schema',
        '$filters',
        '$messages'
    ];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id   = $this->convertId($data['$id']);
        $this->data = $this->convertSchema($data);
    }

    /**
     * Convert schema id to valid OpenApi id
     *
     * @param string $id
     * @return string
     */
    public function convertId(string $id):string
    {
        return trim(str_replace('/', '-', $id), '-');
    }

    /**
     * Get schema id
     *
     * @return string
     */
    public function getId():string
    {
        return $this->id;
    }

    /**
     * Check if property exists in shema properties
     *
     * @param string $property
     * @return bool
     */
    public function hasProperty(string $property):bool
    {
        return array_key_exists($property, $this->data['properties']);
    }

    /**
     * Remove not valid OpenApi properties
     *
     * @param array $data
     * @return array
     */
    protected function removeNotValidProperties(array $data):array
    {
        $validData = array_filter($data, function ($key) {
            return !in_array($key, self::NOT_VALID_PROPERTIES, true);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($validData as &$value) {
            if (is_array($value)) {
                $value = $this->{__FUNCTION__}($value);
            }
        }

        return $validData;
    }

    /**
     * Convert JSON schema to valid OpenApi
     *
     * @param array $data
     * @return array
     */
    protected function convertSchema(array $data):array
    {
        $data = $this->removeNotValidProperties($data);

        array_walk_recursive($data, function (&$value, $key) {
            if ($key === '$ref' && strpos($value, '#') !== -1) {
                list($id, $ref) = explode("#", $value);
                $value = '#/components/schemas/' . $this->convertId($id) . $ref;
            }
        });
        
        return $data;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray():array
    {
        return (array) $this->data;
    }
}
