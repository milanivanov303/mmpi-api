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

    /**
     * Base URI will be removed from route URI
     *
     * @var string
     */
    protected $base_uri;

    /**
     * Route filters
     * 
     * @var array
     */
    protected $filters;

    /**
     * @param array $route
     * @param string $base_uri
     * @param array $filters
     */
    public function __construct(array $route, string $base_uri, array $filters = []) {
        $this->route    = $route;
        $this->base_uri = $base_uri;
        $this->filters  = $filters;
    }

    /**
     * Get route URI
     *
     * @return string
     */
    public function getUri()
    {
        $uri = str_replace($this->base_uri, '', $this->route['uri']);
        return preg_replace('/{(.*):.*}$/', '{$1}', $uri);
    }

    /**
     * Get route method
     *
     * @return string
     */
    public function getMethod()
    {
        return strtolower($this->route['method']);
    }

    /**
     * Get description
     * 
     * @return string
     */
    protected function getDescription()
    {
        if (array_key_exists('description', $this->route['action'])) {
            return $this->route['action']['description'];
        }

        return '';
    }

    /**
     * Get tags
     * 
     * @return array
     */
    protected function getTags()
    {
        if (array_key_exists('tags', $this->route['action'])) {
            return $this->route['action']['tags'];
        }

        $uri = $this->getUri();

        $tag = trim(preg_replace('/\/{(.*)}/', '', $uri), '/');

        return [$tag];
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

    protected function isMethod($method)
    {
        return $this->getMethod() === $method;
    }

    public function setSchema($schema)
    {
        $this->route['action']['schema'] = $schema;
    }

    protected function isSingleResorceUri()
    {
        return preg_match('/{(.*)}$/', $this->getUri());
    }

    protected function getUniqueParameter()
    {
        $matches = [];
        if (preg_match('/{(.*)}$/', $this->getUri(), $matches)) {
            return $matches[1];
        }
        return false;
    }

    protected function isListResorceUri()
    {

        return $this->isMethod('get') && !$this->isSingleResorceUri();
    }

    public function getParameters()
    {
        $parameters = [];
        
        if ($this->isListResorceUri()) {
            foreach($this->filters as $filter) {
                $parameters[] = [
                    'name' => $filter['name'],
                    'in' => 'query',
                    'schema' => [
                        'type' => $filter['type']
                    ]
                ];
            }
        }

        if ($this->isSingleResorceUri()) {
            $parameters[] = [
                'name' => $this->getUniqueParameter(),
                'in' => 'path',
                'schema' => [
                    'type' => 'string'
                ],
                'required' => true
            ];
        }

        if (($this->isMethod('post') || $this->isMethod('put')) && $this->hasSchema()) {
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

    public function getOperationId()
    {
        if (array_key_exists('as', $this->route['action'])) {
            return $this->route['action']['as'];
        }
        return '';
    }

    protected function getResponses()
    {
        $responses = [];
        
        if ($this->isMethod('get')) {

            if ($this->isSingleResorceUri()) {
                $responses['200'] = [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/{$this->getSchema()}"
                            ]
                        ]
                    ]
                ];
            }

            if ($this->isListResorceUri()) {
                $responses['200'] = [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'data' => [
                                        'description' => 'List of media entries',
                                        'items' => [
                                            '$ref' => "#/components/schemas/{$this->getSchema()}"
                                        ],
                                        'type' => 'array'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            }
        }

        if ($this->isMethod('post')) {
            $responses['201'] = [
                'description' => 'Created',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => "#/components/schemas/{$this->getSchema()}"
                        ]
                    ]
                ]
            ];
        }

        $responses['404'] = [
            'description' => 'Not found'
        ];
        $responses['422'] = [
            'description' => 'Unprocessable Entity'
        ];

        return $responses;
    }

    public function toArray()
    {
        return [
            'description' => $this->getDescription(),
            'operationId' => $this->getOperationId(),
            'tags'        => $this->getTags(),
            'parameters'  => $this->getParameters(),
            'responses'   => $this->getResponses(),
            'security'    => [
                ['api_key' => []],
                ['basic_auth' => []]
            ]
        ];
    }
}
