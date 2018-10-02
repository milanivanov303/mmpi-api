<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\FileException;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\FilterContainer;
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
            $routeName = $request->route()[1]['as'] ?? '';

            $schema = $this->getSchema(
                __DIR__ . "/../../../schemas/{$routeName}.json"
            );

            $validator = new Validator();
            $validator->setFilters(
                (new FilterContainer())
                    ->add("string", "checkInDb", new CheckInDbFilter())
            );
            $validator->setLoader(
                new \Opis\JsonSchema\Loaders\File('/', [
                    __DIR__ . "/../../../schemas/"
                ])
            );

            $result = $validator->dataValidation(
                (object)$request->json()->all(),
                $schema,
                PHP_INT_MAX
            );

            if ($result->isValid()) {
                return $next($request);
            }

            $errors = [];
            foreach ($result->getErrors() as $error) {
                $error = new Error($error);
                if (array_key_exists($error->getProperty(), $errors)) {
                    array_push($errors[$error->getProperty()], $error->getMessage());
                } else {
                    $errors[$error->getProperty()] = [$error->getMessage()];
                }
            }

            return response()->json($errors, 422);
        } catch (FileException $e) {
            return response($e->getMessage(), 404);
        }
    }

    /**
     * Get request JSON schema
     *
     * @param string $filename
     * @return object
     * @throws FileException
     */
    protected function getSchema($filename)
    {
        if (!file_exists($filename)) {
            throw new FileException('JSON schema for this request is not found');
        }

        return json_decode(
            file_get_contents($filename)
        );
    }
}
