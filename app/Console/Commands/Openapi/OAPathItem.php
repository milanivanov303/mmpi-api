<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Contracts\Support\Arrayable;
use App\Console\Commands\Openapi\OASchema;

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
    public function __construct(array $route, string $base_uri, array $filters = [])
    {
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

        // if no tags are defined in route get tag from uri
        $tag = trim(preg_replace('/\/{(.*)}/', '', $this->getUri()), '/');

        return [$tag];
    }

    /**
     * Check if there is schema defined for this route
     *
     * @return boolean
     */
    public function hasSchema()
    {
        return array_key_exists('schema', $this->route['action']);
    }

    /**
     * Get route schema if there is one defined
     *
     * @return mixed
     */
    public function getSchema()
    {
        if ($this->hasSchema()) {
            return $this->route['action']['schema'];
        }
        return false;
    }

    /**
     * Check method
     *
     * @param string $method
     * @return boolean
     */
    protected function isMethod($method)
    {
        return $this->getMethod() === $method;
    }

    /**
     * Set route schema
     *
     * @param array $schema
     */
    public function setSchema($schema)
    {
        $this->route['action']['schema'] = $schema;
    }

    /**
     * Check if request is for single resource
     *
     * @return boolean
     */
    protected function isSingleResorceUri()
    {
        return preg_match('/{(.*)}$/', $this->getUri());
    }

    /**
     * Check if request is for list resource
     *
     * @return boolean
     */
    protected function isListResorceUri()
    {
        return $this->isMethod('get') && !$this->isSingleResorceUri();
    }

    /**
     * Get request unique parameter if there is one
     *
     * @return boolean|string
     */
    protected function getUniqueParameter()
    {
        $matches = [];
        if (preg_match('/{(.*)}$/', $this->getUri(), $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Get request parameters
     *
     * @return array
     */
    public function getParameters()
    {
        $parameters = [];
        
        if ($this->isListResorceUri()) {
            foreach ($this->filters as $filter) {
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

        return $parameters;
    }

    /**
     * Load route schema
     *
     * @return false|OASchema
     */
    public function loadSchema()
    {
        if (!$this->hasSchema()) {
            return false;
        }

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

    /**
     * Get operation id from route name
     *
     * @return string
     */
    public function getOperationId()
    {
        if (array_key_exists('as', $this->route['action'])) {
            return $this->route['action']['as'];
        }
        return '';
    }

    /**
     * Get request body
     *
     * @return array
     */
    protected function getRequestBody()
    {
        if (($this->isMethod('post') || $this->isMethod('put')) && $this->hasSchema()) {
            return [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => "#/components/schemas/{$this->getSchema()}"
                        ]
                    ]
                ]
            ];
        }

        return [];
    }

    /**
     * Get responses
     *
     * @return array
     *
     * @todo !!!Refactor this code so it has less hardcoded data
     */
    protected function getResponses()
    {
        $responses = [];

        if ($this->isMethod('post')) {
            $responses['201'] = [
                'description' => 'Created',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    '$ref' => "#/components/schemas/{$this->getSchema()}"
                                ],
                                'meta' => [
                                    'type' => 'object',
                                    'description' => 'Meta data for this request'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        if ($this->isMethod('delete')) {
            $responses['204'] = [
                'description' => 'Deleted',
                'content' => [
                    'text/html' => [
                        'schema' => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ];
        }

        if ($this->isMethod('get') && $this->isListResorceUri()) {
            $responses['200'] = [
                'description' => '',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    'type' => 'array',
                                    'description' => 'List of resource entries',
                                    'items' => [
                                        '$ref' => "#/components/schemas/{$this->getSchema()}"
                                    ]
                                ],
                                'meta' => [
                                    'type' => 'object',
                                    'description' => 'Meta data for this request'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        if ($this->isMethod('put') || ($this->isMethod('get') && $this->isSingleResorceUri())) {
            $responses['200'] = [
                'description' => '',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    '$ref' => "#/components/schemas/{$this->getSchema()}"
                                ],
                                'meta' => [
                                    'type' => 'object',
                                    'description' => 'Meta data for this request'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        if ($this->isSingleResorceUri()) {
            $responses['404'] = [
                'description' => 'Not found',
                'content' => [
                    'text/html' => [
                        'schema' => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ];
        }

        if ($this->isMethod('post') || $this->isMethod('put')) {
            $responses['422'] = [
                'description' => 'Unprocessable Entity',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object'
                            ]
                        ]
                    ]
                ]
            ];
        }

        $responses['500'] = [
            'description' => 'Internal Server Error',
            'content' => [
                'text/html' => [
                    'schema' => [
                        'type' => 'string'
                    ]
                ]
            ]
        ];

        return $responses;
    }

    /**
     * Convert object to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter(
            [
                'description' => $this->getDescription(),
                'operationId' => $this->getOperationId(),
                'tags'        => $this->getTags(),
                'parameters'  => $this->getParameters(),
                'requestBody' => $this->getRequestBody(),
                'responses'   => $this->getResponses(),
                'security'    => [
                    ['api_key' => []]
                ]
            ]
        );
    }
}
