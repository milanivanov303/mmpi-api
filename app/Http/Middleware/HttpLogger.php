<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class HttpLogger
{

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (env('APP_DEBUG', true)) {
            $this->log($request, $response);
        }
        return $response;
    }
    
    /**
     * Log request
     *
     * @param Request $request
     * @param Response $response
     */
    protected function log(Request $request, Response $response)
    {
        Log::channel('http')->info(
            PHP_EOL .
            $this->getRequestCurl($request) .
            PHP_EOL .
            PHP_EOL .
            $response->getStatusCode() . ' ' . $response->getContent().
            PHP_EOL
        );
    }

    /**
     * Get request cURL command
     *
     * @param Request $request
     * @return string
     */
    protected function getRequestCurl(Request $request)
    {
        $curl = [];

        $curl[] = "curl -X {$request->getMethod()}";
        $curl[] = "'{$request->getUri()}'";

        foreach ($request->header() as $header => $value) {
            $curl[] = "-H '{$header}: {$value[0]}'";
        }

        if ($request->json()->all()) {
            $data = json_encode($request->json()->all(), JSON_PRETTY_PRINT);
            $curl[] = "-d '{$data}'";
        }

        return implode(" \\" . PHP_EOL, $curl);
    }
}
