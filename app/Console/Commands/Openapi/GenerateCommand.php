<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Console\Command;
use Laravel\Lumen\Routing\Router;
use Illuminate\Support\Facades\Schema;
use App\Repositories\RepositoryInterface;

/**
 * Generate API documentation
 *
 * @category Console_Command
 */
class GenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "openapi:generate {version=v1}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate API documentation";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Router $router)
    {

        $baseUri = "/api/{$this->argument('version')}";

        $openapi = [
            'openapi' => '3.0.1',
            'info' => [
                'description' => 'MMPI API',
                'version'     => '1',
                'title'       => 'MMPI API'
            ],
            'servers' => [
                [
                    'url' => "http://yarnaudov.codixfr.private:8111{$baseUri}/"
                ],
                [
                    'url' => "http://localhost:8111{$baseUri}/"
                ]
            ],
            'security' => [
                ['api_key' => []]
            ],
            'components' => [
                'parameters' => [
                    'limit' => [
                        'name' => 'limit',
                        'in' => 'query',
                        'schema' => [
                            'type' => 'integer',
                            'description' => 'Limit results. It is ignored when pagination is used',
                            'example' => 50
                        ]
                    ],
                    'order_by' => [
                        'name' => 'order_by',
                        'in' => 'query',
                        'schema' => [
                            'type' => 'string',
                            'description' => 'Order results by given property'
                        ]
                    ],
                    'order_dir' => [
                        'name' => 'order_dir',
                        'in' => 'query',
                        'schema' => [
                            'type' => 'string',
                            'description' => 'Direction to use when ordering results',
                            'enum' => ['asc', 'desc']
                        ]
                    ],
                    'page' => [
                        'name' => 'page',
                        'in' => 'query',
                        'schema' => [
                            'type' => 'integer',
                            'description' => 'Return given page from paginated results'
                        ]
                    ],
                    'per_page' => [
                        'name' => 'per_page',
                        'in' => 'query',
                        'schema' => [
                            'type' => 'integer',
                            'description' => 'Set results per page',
                            'example' => 15
                        ],
                    ],
                    'fields' => [
                        'name' => 'fields',
                        'in' => 'query',
                        'schema' => [
                            'oneOf' => [
                                ['type' => 'string'],
                                [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ],
                            'description' => 'Return only listed fields in results',
                            'example' => 'field1, field2'
                        ],
                    ]
                ],
                'securitySchemes' => [
                    'api_key' => [
                        'in' => 'header',
                        'name' => 'X-AUTH-TOKEN',
                        'type' => 'apiKey'
                    ],
                    //'basic_auth' => [
                    //    'type' => 'http',
                    //    'scheme' => 'basic'
                    //]
                ]
            ]
        ];

        $document = new OADocument($openapi);

        foreach ($router->getRoutes() as $route) {
            $pathItem = new OAPathItem($route, $baseUri);
            
            $filters = $this->getRouteFilters($route);
            if ($filters) {
                $pathItem->setFilters($filters);
            }

            $document->addPathItem($pathItem);
        }

        echo $document->toJson();

        file_put_contents(storage_path('openapi.json'), $document->toJson());
    }

    /**
     * Get route filters
     *
     * @param array $route
     * @return array
     */
    protected function getRouteFilters($route)
    {
        $controller = current(explode('@', $route['action']['uses']));
        $filters    = [];

        try {
            $model = $this->getModelInstance($controller);
            return $this->getModelFilters($model);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

         return $filters;
    }

    /**
     * Get Model instance
     *
     * @param string $controller
     * @return
     */
    protected function getModelInstance($controller)
    {
        $class = (new \ReflectionParameter([$controller, '__construct'], 'model'))
                    ->getClass();

        $instance = app($class->getName());
        if ($instance instanceof RepositoryInterface) {
            return $instance->getModel();
        }

        return $class->newInstance();
    }

    /**
     * Get model filters
     *
     * @param type $model
     * @return array
     */
    protected function getModelFilters($model)
    {
        $filters = [];
        
        if (method_exists($model, 'getFilterableAttributes')) {
            foreach ($model->getFilterableAttributes() as $column) {
                $name = $column;
                // Get mapped attributes if model uses mappable trait
                if (method_exists($model, 'getMappededAttribute')) {
                    $name = $model->getMappededAttribute($column, $model::$MAP_RESPONSE_VALUES);
                }
                array_push($filters, [
                    'name' => $name,
                    'type' => 'string'//Schema::getColumnType($model->getTable(), $column)
                ]);
            }
        }

        return $filters;
    }
}
