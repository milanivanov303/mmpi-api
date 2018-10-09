<?php

namespace App\Http\Middleware;

use Closure;
use Opis\JsonSchema\Validator as OpisValidator;
use Opis\JsonSchema\Exception\AbstractSchemaException as OpisSchemaException;
use App\Helpers\JsonSchema\Filters\CheckInDbFilter;
use App\Helpers\JsonSchema\Error;

class JsonValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Skip json validation for GET requests
        if ($request->isMethod('GET') || $request->isMethod('DELETE')) {
            return $next($request);
        }

        try {
            $validator = new OpisValidator();
            $validator->setLoader($this->getLoader());
            $validator->setFilters($this->getFilters());

            $schema = $validator->getLoader()->loadSchema(
                $this->getRouteSchema($request->route())
            );

            $result = $validator->schemaValidation(
                (object)$request->json()->all(),
                $schema,
                PHP_INT_MAX
            );

            if ($result->isValid()) {
                return $next($request);
            }

            return response()->json($this->getConvertedErrors($result), 422);
        } catch (OpisSchemaException $e) {
            return response($e->getMessage(), 404);
        }
    }

    /**
     * Get request route JSON schema name
     *
     * @param array $route
     * @return string
     */
    protected function getRouteSchema($route)
    {
        return $route[1]['schema'] ?? '';
    }

    /**
     * Create JSON schema loader
     *
     * @return \Opis\JsonSchema\ISchemaLoader
     */
    protected function getLoader()
    {
        return new \Opis\JsonSchema\Loaders\File('/api', [
            base_path("schemas"),
            base_path("schemas/api")
        ]);
    }

    /**
     * Ger JSON schema filters
     *
     * @return \Opis\JsonSchema\IFilterContainer
     */
    protected function getFilters()
    {
        $filterContainer = new \Opis\JsonSchema\FilterContainer();
        return $filterContainer->add("string", "checkInDb", new CheckInDbFilter());
    }

    /**
     * Get human readable errors
     *
     * @param \Opis\JsonSchema\ValidationResult $result
     * @return array
     */
    protected function getConvertedErrors($result)
    {
        $errors = [];
        foreach ($result->getErrors() as $error) {
            $error = new Error($error);
            if (array_key_exists($error->getProperty(), $errors)) {
                array_push($errors[$error->getProperty()], $error->getMessage());
            } else {
                $errors[$error->getProperty()] = [$error->getMessage()];
            }
        }
        return $errors;
    }
}
