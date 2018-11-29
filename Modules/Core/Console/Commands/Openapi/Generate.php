<?php

namespace Modules\Core\Console\Commands\Openapi;

use Illuminate\Console\Command;
use Laravel\Lumen\Routing\Router;
use Modules\Core\Repositories\RepositoryInterface;
use Modules\Core\Helpers\ModelFilter;
use Modules\Core\Models\Model;

/**
 * Generate API documentation
 *
 * @category Console_Command
 */
class Generate extends Command
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
     * @param Router $router
     * @return mixed
     */
    public function handle(Router $router)
    {
        $document = new OADocument($this->loadDefaultDocument());
        $document->setSchemas($this->loadSchemas());

        foreach ($router->getRoutes() as $route) {
            $pathItem = new OAPathItem($route);

            // set route base URI
            $pathItem->setBaseUri($this->baseUri());

            // set path item schema
            if (array_key_exists('schema', $route['action'])) {
                $schema = $document->getSchema($route['action']['schema']);
                if ($schema) {
                    $pathItem->setSchema($schema);
                }
            }

            // set path item filters
            $filters = $this->getRouteFilters($route);
            if ($filters) {
                $pathItem->setFilters($filters);
            }

            $document->addPathItem($pathItem);
        }

        file_put_contents(
            base_path('public/openapi.json'),
            $document->toJson()
        );
    }

    protected function baseUri()
    {
        return "/api/{$this->argument('version')}";
    }

    /**
     * Load default document
     *
     * @return array
     */
    protected function loadDefaultDocument()
    {
        // file location can be added to config if needed!
        $filename = base_path('openapi.json');

        if (!file_exists($filename)) {
            return [];
        }

        return json_decode(
            file_get_contents($filename),
            JSON_OBJECT_AS_ARRAY
        );
    }

    /**
     * Load route schema
     *
     * @return OASchema[]
     */
    protected function loadSchemas() : array
    {
        $schemas = [];

        $dir = base_path('schemas'); // this should come from config

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir . $this->baseUri())
        );

        foreach ($files as $file) {
            if (is_file($file)) {
                $schema = $this->loadSchema($file);
                if ($schema) {
                    $schemas[str_replace($dir, '', $file)] = $schema;
                }
            }
        }

        return $schemas;
    }

    /**
     * Load route schema
     *
     * @return false|OASchema
     */
    protected function loadSchema($filename)
    {
        if (file_exists($filename)) {
            $schema = json_decode(
                file_get_contents($filename),
                JSON_OBJECT_AS_ARRAY
            );

            if (json_last_error() === JSON_ERROR_NONE) {
                return new OASchema($schema);
            }
        }

        return false;
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
        } catch (\ReflectionException $e) {
            //var_dump($e->getMessage());
        }

        return $filters;
    }

    /**
     * Get Model instance
     *
     * @param string $controller
     * @return Model
     *
     * @throws \ReflectionException
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
     * @param Model $model
     * @return array
     */
    protected function getModelFilters(Model $model)
    {
        // Get model filters
        $filters = (new ModelFilter($model))->getFilterableAttributes();

        // Map model filters names
        $filters = array_flip(
            $model->mapper->mapResponseData(array_flip($filters))
        );

        array_walk($filters, function (&$filter) {
            $filter = [
                'name' => $filter,
                'type' => 'string'
            ];
        });

        return $filters;
    }
}
