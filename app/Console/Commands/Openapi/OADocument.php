<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class OADocument implements Arrayable, Jsonable
{

    protected $info;
    protected $servers;
    
    /**
     * All available path items
     * 
     * @var [PathItem]
     */
    protected $pathItems;

    /**
     * All available JSON schemas
     *
     * @var array
     */
    protected $schemas;

    /**
     * Openapi document version
     *
     * @var type
     */
    protected $oa_version = '3.0.1';

    public function __construct($info, $servers) {
        $this->info = $info;
        $this->servers = $servers;
    }

    public function addPathItem(OAPathItem $pathItem)
    {
        $this->pathItems[] = $pathItem;
    }

    protected function getPathItems()
    {
        $pathItems = [];
        foreach($this->pathItems as $pathItem) {

            if ($pathItem->hasSchema()) {
                $schema = $pathItem->loadSchema();
                if ($schema) {
                    $this->addSchema($schema);
                }
            }

            $pathItems[$pathItem->getUri()][$pathItem->getMethod()] = $pathItem->toArray();
        }
        return $pathItems;
    }

    protected function addSchema($schema)
    {
        $this->schemas[] = $schema;
    }

    protected function getSchemas()
    {
        $schemas = [];
        foreach($this->schemas as $schema) {
            $schemas[$schema->getId()] = $schema->toArray();
        }
        return $schemas;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {

        return [
            'openapi' => $this->oa_version,
            'info' => $this->info,
            'servers' => $this->servers,
            'paths' => $this->getPathItems(),
            'components' => [
                'schemas' => $this->getSchemas()
            ]
        ];

    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }
}
