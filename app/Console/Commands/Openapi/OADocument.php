<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class OADocument implements Arrayable, Jsonable
{
    /**
     * Openapi document
     *
     * @var array
     */
    protected $document;
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
    
    public function __construct(array $document)
    {
        $this->document = $document;
    }

    public function addPathItem(OAPathItem $pathItem)
    {
        $this->pathItems[] = $pathItem;
    }

    protected function getPathItems()
    {
        $pathItems = [];
        foreach ($this->pathItems as $pathItem) {
            $schema = $pathItem->loadSchema();
            if ($schema) {
                $this->addSchema($schema);
            }

            $pathItems[$pathItem->getUri()][$pathItem->getMethod()] = $pathItem->toArray();
        }
        return $pathItems;
    }

    /**
     * Add schema to document
     * 
     * @param OASchema $schema
     */
    protected function addSchema($schema)
    {
        $this->schemas[] = $schema;
    }

    /**
     * Get document schemas
     * 
     * @return array
     */
    protected function getSchemas()
    {
        $schemas = [];
        foreach ($this->schemas as $schema) {
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

        return array_merge_recursive(
            $this->document,
            [
                'paths' => $this->getPathItems(),
                'components' => [
                    'schemas' => $this->getSchemas()
                ]
            ]
        );
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
