<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class OADocument implements Arrayable, Jsonable
{
    /**
     * OpenApi document
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

    /**
     * Add path item to document
     *
     * @param OAPathItem $pathItem
     */
    public function addPathItem(OAPathItem $pathItem)
    {
        $this->pathItems[] = $pathItem;
    }

    /**
     * Get document path items
     *
     * @return array
     */
    protected function getPathItems():array
    {
        $pathItems = [];
        foreach ($this->pathItems as $pathItem) {
            $schema = $pathItem->getSchema();
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
    protected function addSchema(OASchema $schema)
    {
        $this->schemas[] = $schema;
    }

    /**
     * Get document schemas
     *
     * @return array
     */
    protected function getSchemas():array
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
    public function toArray():array
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
    public function toJson(int $options = 0):string
    {
        return json_encode($this->toArray());
    }
}
