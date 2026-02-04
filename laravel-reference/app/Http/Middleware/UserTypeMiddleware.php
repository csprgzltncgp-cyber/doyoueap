<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (strpos((string) $role, '-')) {
            $roles = explode('-', (string) $role);
            $has_role = 0;
            foreach ($roles as $value) {
                if ($value == Auth::user()->type) {
                    $has_role = 1;
                }
            }
            // if has the role
            if ($has_role != 1) {
                return redirect()->route(Auth::user()->type.'.dashboard');
            }
        } elseif (Auth::user()->type != $role) {
            if ($role == 'superuser' && ! Auth::user()->super_user) {
                return redirect()->route(Auth::user()->type.'.dashboard');
            }
        }

        return $next($request);
    }
}
