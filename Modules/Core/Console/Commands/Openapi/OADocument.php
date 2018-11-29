<?php

namespace Modules\Core\Console\Commands\Openapi;

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
    protected function getPathItemsArray():array
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
    public function addSchema(OASchema $schema)
    {
        $this->schemas[] = $schema;
    }

    /**
     * Set document schemas
     *
     * @param array $schemas
     */
    public function setSchemas(array $schemas)
    {
        $this->schemas = $schemas;
    }

    /**
     * Convert JSON schema to valid OpenApi
     *
     * @param array $data
     * @return array
     */
    protected function fixRefs(array $data) : array
    {
        $function = __FUNCTION__;

        array_walk($data, function (&$value, $key) use ($function) {

            if (is_array($value)) {
                $value = $this->{$function}($value);
                return;
            }

            if ($key === '$ref') {
                if (strpos($value, '#') !== false) {
                    list($id, $ref) = explode("#", $value);
                }

                if (array_key_exists($id ?? $value, $this->schemas)) {
                    $value = '#/components/schemas/' . $this->schemas[$id ?? $value]->getId() . ($ref ?? '');
                }
            }
        });

        return $data;
    }

    /**
     * Get document schema
     *
     * @param string $id
     * @return OASchema|false
     */
    public function getSchema($id)
    {
        if (array_key_exists($id, $this->schemas)) {
            return $this->schemas[$id];
        }

        return false;
    }

    /**
     * Get document schemas
     *
     * @return array
     */
    protected function getSchemasArray():array
    {
        $schemas = [];
        foreach ($this->schemas as $schema) {
            $schemas[$schema->getId()] = $this->fixRefs($schema->toArray());
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
                'paths' => $this->getPathItemsArray(),
                'components' => [
                    'schemas' => $this->getSchemasArray()
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
