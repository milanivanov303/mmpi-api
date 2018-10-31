<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\JsonSchema\Filters\CheckInDbFilter;
use App\Helpers\JsonSchema\Error;

class JsonValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip json validation for GET and DELETE requests
        if ($request->isMethod('GET') || $request->isMethod('DELETE')) {
            return $next($request);
        }

        try {
            $validator = new \Opis\JsonSchema\Validator;
            $validator->setLoader($this->getLoader());
            $validator->setFilters($this->getFilters());

            $schema = $validator->getLoader()->loadSchema(
                $this->getRouteSchema($request->route())
            );

            // TODO: refactor this when there is time
            $data = json_decode(json_encode($request->json()->all()), false);

            $result = $validator->schemaValidation(
                $data,
                $schema,
                PHP_INT_MAX
            );

            if ($result->isValid()) {
                return $next($request);
            }

            return response()->json($this->getConvertedErrors($result), 422);
        } catch (\Opis\JsonSchema\Exception\AbstractSchemaException $e) {
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

        $filterContainer->add("string", "checkInDb", new CheckInDbFilter);
        $filterContainer->add("null", "checkInDb", new CheckInDbFilter);

        return $filterContainer;
    }

    /**
     * Get human readable errors
     *
     * @param \Opis\JsonSchema\ValidationResult $result
     * @return array
     */
    protected function getConvertedErrors(\Opis\JsonSchema\ValidationResult $result)
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
