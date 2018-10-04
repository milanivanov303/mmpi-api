<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Contracts\Support\Arrayable;

class OAPathItem implements Arrayable
{
    /**
     * Route 
     *
     * @var array
     */
    protected $route;

    public function __construct(array $route) {
        $this->route = $route;
    }

    public function getUri()
    {
        //$uri = str_replace("/api/{$this->argument('version')}", '', $this->route['uri']);
        return preg_replace('/{(.*):.*}$/', '{$1}', $this->route['uri']);
    }

    public function getMethod()
    {
        return strtolower($this->route['method']);
    }

    protected function getDescription()
    {
        if (array_key_exists('description', $this->route['action'])) {
            return $this->route['action']['description'];
        }

        return '';
    }

    protected function getTags()
    {
        if (array_key_exists('tags', $this->route['action'])) {
            return $this->route['action']['tags'];
        }

        return [];
    }

    public function hasSchema()
    {
        return array_key_exists('schema', $this->route['action']);
    }

    public function getSchema()
    {
        if ($this->hasSchema()) {
            return $this->route['action']['schema'];
        }
        return false;
    }

    public function setSchema($schema)
    {
        $this->route['action']['schema'] = $schema;
    }

    public function getParameters()
    {
        $parameters = [];

        if (preg_match('/{(.*)}$/', $this->getUri(), $matches)) {
            $parameters[] = [
                'name' => $matches[1],
                'in' => 'path',
                'schema' => [
                    'type' => 'string'
                ],
                'required' => true
            ];
        }

        if (($this->getMethod() == 'post' || $this->getMethod() == 'put') && $this->hasSchema()) {
            $parameters[] = [
                'name' => 'body',
                'in' => 'query',
                'schema' => [
                    '$ref' => "#/components/schemas/{$this->getSchema()}"
                ],
                'required' => true
            ];
        }

        return $parameters;
    }

    public function loadSchema()
    {
        $filename = base_path('schemas/' . $this->getSchema());

        if (file_exists($filename)) {
            
            $schema = new OASchema(json_decode(
                file_get_contents($filename),
                JSON_OBJECT_AS_ARRAY
            ));

            if ($schema) {
                $this->setSchema($schema->getId());
                return $schema;
            }
        }

        return false;
    }

    public function toArray()
    {
        return [
            'description' => $this->getDescription(),
            //'operationId' => $uri,
            'tags' => $this->getTags(),
            'parameters' => $this->getParameters(),
            'responses' => [
                '200' => ['description' => 'OK']
            ]
        ];
    }
}
