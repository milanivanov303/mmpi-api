<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Console\Command;
use Laravel\Lumen\Routing\Router;

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
                    'basic_auth' => [
                        'type' => 'http',
                        'scheme' => 'basic'
                    ]
                ],
                'schemas' => [
                    'responses-success' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => [
                                'description' => 'List of media entries',
                                'items' => [
                                    '$ref' => '#/components/schemas/api-v1-hashes-create.json'
                                ],
                                'type' => 'array'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $document = new OADocument($openapi);

        foreach($router->getRoutes() as $route) {
            if (strlen($route['uri']) > 1) {
                $document->addPathItem(new OAPathItem($route, $base_uri));
            }
        }

        echo $document->toJson();

        file_put_contents(storage_path('openapi.json'), $document->toJson());

        //echo json_encode($docs);

    }
}