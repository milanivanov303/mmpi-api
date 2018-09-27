<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class HttpLoggerMiddleware
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
        if (env('APP_DEBUG', true)) {
            $this->log($request);
        }
        return $next($request);
    }
    
    /**
     * Log request
     * 
     * @param Request $request
     * @param Response $response
     */
    protected function log(Request $request)
    {
        // generate curl command here!
        Log::channel('http')->info($request->__toString());
    }
}