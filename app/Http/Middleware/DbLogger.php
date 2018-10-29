<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;

class DbLogger
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
            Event::listen(
                QueryExecuted::class,
                function ($query) {
                    $this->log($query);
                }
            );
        }

        return  $next($request);
    }

    /**
     * Log request
     *
     * @param QueryExecuted $query
     */
    protected function log(QueryExecuted $query)
    {
        Log::channel('db')->info(
            PHP_EOL .
            $query->sql .
            PHP_EOL .
            'bindings: ' . implode(', ', $query->bindings) .
            PHP_EOL .
            'time: ' . $query->time .
            PHP_EOL
        );
    }
}
