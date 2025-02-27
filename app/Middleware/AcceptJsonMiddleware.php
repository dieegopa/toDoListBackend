<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptJsonMiddleware
{
    /**
     * Check the incoming request and set the Accept header to application/json
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
