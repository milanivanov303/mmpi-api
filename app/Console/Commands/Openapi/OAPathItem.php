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
    protected $baseUri = '';

    /**
     * Route filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Route schema
     * @var OASchema
     */
    protected $schema;

    /**
     * Base $ref to schemas
     */
    protected $schemasBaseUri = '#/components/schemas/';

    /**
     * @param array $route
     */
    public function __construct(array $route)
    {
        $this->route   = $route;
    }

    /**
     * Set base URI
     *
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * Set route filters
     *
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Set route filters
     *
     * @param OASchema $schema
     */
    public function setSchema(OASchema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Check if there is schema defined for this route
     *
     * @return bool
     */
    public function hasSchema() : bool
    {
        return $this->schema instanceof OASchema;
    }

    /**
     * Get route schema if there is one defined
     *
     * @return OASchema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get route URI
     *
     * @return string
     */
    public function getUri():string
    {
        $uri = str_replace($this->baseUri, '', $this->route['uri']);
        return preg_replace('/{(.*):.*}$/', '{$1}', $uri);
    }

    /**
     * Get route method
     *
     * @return string
     */
    public function getMethod():string
    {
        return strtolower($this->route['method']);
    }

    /**
     * Get link to schema resource
     *
     * @param string $resource
     * @return string
     */
    protected function getLinkToSchema(string $resource = ''):string
    {
        if ($this->hasSchema()) {
            return trim(
                "{$this->schemasBaseUri}{$this->getSchema()->getId()}/{$resource}",
                '/'
            );
        }

        return '';
    }

    /**
     * Get description
     *
     * @return string
     */
    protected function getDescription():string
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
    protected function getTags():array
    {
        if (array_key_exists('tags', $this->route['action'])) {
            return $this->route['action']['tags'];
        }

        // if no tags are defined in route get tag from uri
        $tag = trim(preg_replace('/\/{(.*)}/', '', $this->getUri()), '/');

        return [$tag];
    }

    /**
     * Check method
     *
     * @param string $method
     * @return boolean
     */
    protected function isMethod($method):bool
    {
        return $this->getMethod() === $method;
    }

    /**
     * Check if request is for single resource
     *
     * @return boolean
     */
    protected function isSingleResorceUri():bool
    {
        return preg_match('/{(.*)}$/', $this->getUri());
    }

    /**
     * Check if request is for list resource
     *
     * @return boolean
     */
    protected function isListResorceUri():bool
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
     * Get parameter schema
     *
     * @param string $parameter
     * @return array
     */
    protected function getParameterSchema(string $parameter):array
    {
        if ($this->hasSchema() && $this->getSchema()->hasProperty($parameter)) {
            return [
                '$ref' => $this->getLinkToSchema("properties/{$parameter}")
            ];
        }

        return ['type' => 'string'];
    }

    /**
     * Get request parameters
     *
     * @return array
     */
    protected function getParameters():array
    {
        $parameters = [];

        if ($this->isSingleResorceUri()) {
            $uniqueParameter = $this->getUniqueParameter();
            array_push($parameters, [
                'name' => $uniqueParameter,
                'in' => 'path',
                'schema' => $this->getParameterSchema($uniqueParameter),
                'required' => true
            ]);
        }

        if ($this->isListResorceUri()) {
            foreach ($this->filters as $filter) {
                array_push($parameters, [
                    'name'   => $filter['name'],
                    'in'     => 'query',
                    'schema' => $this->getParameterSchema($filter['name']),
                ]);
            }

            $parameters = array_merge($parameters, [
                [
                    'name'   => 'order_by',
                    'in'     => 'query',
                    'schema' => [
                        'allOf' => [
                            ['$ref' => '#/components/parameters/order_by/schema'],
                            ['enum' => array_keys($this->getSchema()->toArray()['properties'])]
                        ]
                    ]
                ],
                ['$ref' => '#/components/parameters/order_dir'],
                ['$ref' => '#/components/parameters/limit'],
                ['$ref' => '#/components/parameters/page'],
                ['$ref' => '#/components/parameters/per_page'],
                ['$ref' => '#/components/parameters/fields']
            ]);
        }

        return $parameters;
    }

    /**
     * Get operation id from route name
     *
     * @return string
     */
    public function getOperationId():string
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
    protected function getRequestBody():array
    {
        if (($this->isMethod('post') || $this->isMethod('put')) && $this->hasSchema()) {
            return [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => $this->getLinkToSchema()
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
    protected function getResponses():array
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
                                    '$ref' => $this->getLinkToSchema()
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

        if ($this->isMethod('get') && $this->isListResorceUri()) {
            $responses['200'] = [
                'description' => 'Success',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    'type' => 'array',
                                    'description' => 'List of resource entries',
                                    'items' => [
                                        '$ref' => $this->getLinkToSchema()
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
                'description' => 'Success',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    '$ref' => $this->getLinkToSchema()
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
            $responses['204'] = ['$ref' => '#/components/responses/Deleted'];
        }

        $responses['401'] = ['$ref' => '#/components/responses/Unauthorized'];

        if ($this->isSingleResorceUri()) {
            $responses['404'] = ['$ref' => '#/components/responses/NotFound'];
        }

        if ($this->isMethod('post') || $this->isMethod('put')) {
            $responses['422'] = ['$ref' => '#/components/responses/ValidationError'];
        }

        $responses['500'] = ['$ref' => '#/components/responses/InternalServerError'];

        return $responses;
    }

    /**
     * Convert object to array
     *
     * @return array
     */
    public function toArray():array
    {
        $pathItem = [
            'description' => $this->getDescription(),
            'operationId' => $this->getOperationId(),
            'tags'        => $this->getTags(),
            'parameters'  => $this->getParameters(),
            'requestBody' => $this->getRequestBody(),
            'responses'   => $this->getResponses(),
            'security'    => [
                [
                    'xAuthToken' => [],
                    'bearerAuth' => []
                ]
            ]
        ];

        if (array_key_exists('openapi', $this->route['action'])) {
            $pathItem = array_merge($pathItem, $this->route['action']['openapi']);
        }

        return array_filter($pathItem);
    }
}
