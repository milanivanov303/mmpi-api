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
    public function hasProperty(string $property) : bool
    {
        // TODO: get schema properties if it is extending another schema
        if (!array_key_exists('properties', $this->data)) {
            return false;
        }
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
        $function = __FUNCTION__;

        $data = $this->removeNotValidProperties($data);

        array_walk($data, function (&$value, $key) use ($function) {

            if (is_array($value)) {
                if (array_key_exists('type', $value) && $key !== 'properties') {
                    $value = $this->fixNotValidTypeProperty($value);
                }

                $value = $this->{$function}($value);

                return;
            }
        });

        $data = array_filter($data, [$this, 'removeEmptyProperties']);

        return $data;
    }

    protected function removeEmptyProperties($data)
    {
        if (is_array($data)) {
            return array_filter($data, [$this, __FUNCTION__]);
        }

        return !empty($data);
    }

    /**
     * Convert type property to valid openapi
     *
     * @param $value
     * @return mixed
     */
    protected function fixNotValidTypeProperty($value)
    {
        if (is_string($value['type'])) {
            // if type is null set nullable property
            if ($value['type'] === "null") {
                $value['nullable'] = true;
                unset($value['type']);
                return $value;
            }
            // don't do anything if type is string
            return $value;
        }

        // check if there is null type and set nullable property
        $nullKey = array_search('null', $value['type']);
        if ($nullKey !== false) {
            $value['nullable'] = true;
        }
        unset($value['type'][$nullKey]);

        // use only one type as openapi do not support multiple types
        $value['type'] = current($value['type']);

        return $value;
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
