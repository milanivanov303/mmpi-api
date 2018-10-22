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
        // generate curl command here, so it can be used directly!
        Log::channel('http')->info($request->__toString());
    }
}
