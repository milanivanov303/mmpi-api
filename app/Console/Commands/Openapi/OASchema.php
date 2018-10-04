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

    protected function convertSchema($data)
    {
        array_walk_recursive($data, function(&$value, &$key) {
            if ($key === '$ref' && strpos($value, '#') !== -1) {
                list($id, $ref) = explode("#", $value);
                $value = '#/components/schemas/' .$this->convertId($id). $ref;
            }
            if($key === '$filters') {
                unset($key);
            }
        });
        
        unset($data['$id'], $data['$schema']);
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

