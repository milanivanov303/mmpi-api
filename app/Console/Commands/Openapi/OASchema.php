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

    protected $data;

    const NOT_VALID_PROPERTIES = [
        '$id',
        '$schema',
        '$filters',
        'messages'
    ];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id   = $this->convertId($data['$id']);
        $this->data = $this->convertSchema($data);
    }

    public function convertId($id)
    {
        return trim(str_replace('/', '-', $id), '-');
    }

    public function getId()
    {
        return $this->id;
    }

    protected function removeNotValidProperties($data)
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

    protected function convertSchema($data)
    {

        $data = $this->removeNotValidProperties($data);

        array_walk_recursive($data, function(&$value, $key) {
            if ($key === '$ref' && strpos($value, '#') !== -1) {
                list($id, $ref) = explode("#", $value);
                $value = '#/components/schemas/' .$this->convertId($id). $ref;
            }
        });
        
        return $data;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}

