<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CgpTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (request()->bearerToken() != config('app.cgp_internal_authentication_token')) {
            abort(403);
        }

        return $next($request);
    }
}
