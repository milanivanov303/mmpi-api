<?php

namespace App\Http\Middleware;

use JsonSchema\Validator;
use JsonSchema\Exception\ExceptionInterface;
use Closure;
use App\Exceptions\FileException;

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
        try {
            //$request->route()->getName() - this is correct way to access route name. but it is not working!

            $routeName = $request->route()[1]['as'] ?? '';

            $scheme = $this->getScheme(
                __DIR__ . "/../../../schemes/{$routeName}.json"
            );

            $data = (object)$request->json()->all();

            $validator = new Validator;
            $validator->validate($data, $scheme);
            //$validator->coerce($data, $scheme);

            if (!$validator->isValid()) {
                return response()->json($validator->getErrors(), 422);
            }

            return $next($request);
        } catch (ExceptionInterface $e) {
            return response($e->getMessage(), 400);
        } catch (FileException $e) {
            // if no scheme defined skip validation
            return $next($request);
        }
    }

    /**
     * Get request JSON scheme
     *
     * @param string $filename
     * @return object
     * @throws FileException
     */
    protected function getScheme($filename)
    {
        if (!file_exists($filename)) {
            throw new FileException('JSON scheme not found');
        }

        return json_decode(
            file_get_contents($filename)
        );
    }
}
