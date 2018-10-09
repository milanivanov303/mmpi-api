<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Console\Command;
use Laravel\Lumen\Routing\Router;
use Illuminate\Support\Facades\Schema;

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

        $base_uri = "/api/{$this->argument('version')}";

        $openapi = [
            'openapi' => '3.0.1',
            'info' => [
                'description' => 'MMPI API',
                'version'     => '1',
                'title'       => 'MMPI API'
            ],
            'servers' => [
                [
                    'url' => "http://yarnaudov.codixfr.private:8111{$base_uri}/"
                ],
                [
                    'url' => "http://localhost:8111{$base_uri}/"
                ]
            ],
            'security' => [
                ['api_key' => []]
            ],
            'components' => [
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
            if (strlen($route['uri']) > 1) {
                $filters = $this->getRouteFilters($route);
                $document->addPathItem(new OAPathItem($route, $base_uri, $filters));
            }
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
            $model = (new \ReflectionParameter([$controller, '__construct'], 'model'))
                        ->getClass()
                        ->newInstance();

            foreach ($model->getFilterAttributes() as $column) {
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
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

         return $filters;
    }
}
