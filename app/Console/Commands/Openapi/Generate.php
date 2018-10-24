<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Console\Command;
use Laravel\Lumen\Routing\Router;
use App\Repositories\RepositoryInterface;
use App\Helpers\ModelFilter;
use App\Models\Model;

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
        $baseUri = "/api/{$this->argument('version')}";

        $document = new OADocument($this->loadDefaultDocument());

        foreach ($router->getRoutes() as $route) {
            $pathItem = new OAPathItem($route, $baseUri);
            
            $filters = $this->getRouteFilters($route);
            if ($filters) {
                $pathItem->setFilters($filters);
            }

            $document->addPathItem($pathItem);
        }

        //echo $document->toJson();

        file_put_contents(
            base_path('public/openapi.json'),
            $document->toJson()
        );
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
            var_dump($e->getMessage());
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

        array_walk($filters, function (&$filter){
            $filter = [
                'name' => $filter,
                'type' => 'string'
            ];
        });

        return $filters;
    }
}
