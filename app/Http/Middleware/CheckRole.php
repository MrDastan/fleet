<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        return $next($request);
    }
}
