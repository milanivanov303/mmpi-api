<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

use Laravel\Lumen\Routing\Router;

use erasys\OpenApi\Spec\v3 as OASv3;

/**
 * Generate API documentation
 *
 * @category Console_Command
 */
class GenerateDocsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "generate:docs {version=v1}";

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
    public function handle()
    {
        $routes = Route::getRoutes();

        $docs = [
            "openapi" => "3.0.1",
            "info" => [
                "description" => "MMPI API",
                "version" => "1",
                 "title" => "MMPI API"
            ],
            "servers" => [
                [
                    "url" => "http://yarnaudov.codixfr.private:8111/api/{$this->argument('version')}/"
                ]
            ]
        ];

        $docs['paths'] = [];
        foreach($routes as $route) {
            $uri    = $this->getUri($route);
            $method = $this->getMethod($route);
            
            if (strlen($uri) > 1) {

                $schema = $this->getSchema($route);
                //    __DIR__ . "/../../../schemas/{$routeName}.json"
                //);

                if ($schema) {
                   $docs['components']['schemas'][$schema->{'$id'}] = $schema;
                }

                $path = [
                    'description' => isset($route['action']['description']) ? $route['action']['description'] : '',
                    'responses' => [
                        '200' => ['description' => 'OK']
                    ]
                ];

                $parameters = $this->getParameters($uri, $method);
                if ($parameters) {
                    $path['parameters'] = $parameters;
                }

                $docs['paths'][$uri][$method] = $path;
            }
        }

        echo json_encode($docs);

    }

    protected function getUri($route)
    {
        $uri = str_replace("/api/{$this->argument('version')}", '', $route['uri']);
        return preg_replace('/{(.*):.*}$/', '{$1}', $uri);
    }

    protected function getMethod($route)
    {
        return strtolower($route['method']);
    }

    protected function getSchema($route)
    {

        $uri = preg_replace('/{.*}$/', '', $route['uri']);

        $filename = __DIR__ . "/../../../schemas/{$uri}";

        switch($route['method']) {
            case 'POST':
                $filename .= '/create.json';
            break;
            case 'PUT':
                $filename = '/update.json';
            break;
        }

        if (file_exists($filename)) {  
            return json_decode(
                file_get_contents($filename)
            );
        }

        return false;
    }

    protected function getParameters($uri, $method)
    {
        $parameters = [];

        if (preg_match('/{(.*)}$/', $uri, $matches)) {
            $parameters[] = [
                'name' => $matches[1],
                'in' => 'path',
                'schema' => [
                    'type' => 'string'
                ],
                'required' => true
            ];
        }

        if ($method == 'post' || $method == 'put') {
            $parameters[] = [
                'name' => 'body',
                'in' => 'query',
                'schema' => [
                    '$ref' => $uri
                ],
                'required' => true
            ];
        }

        return $parameters;
    }

}