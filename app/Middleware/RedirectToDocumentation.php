<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToDocumentation
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('/')) {
            return redirect('/api/documentation');
        }

        return $next($request);
    }
}
