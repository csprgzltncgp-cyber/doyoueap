<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class WelcomePageSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):((Response | RedirectResponse))  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (session('myAdminId') || ! empty(Auth::user()->last_login_at) || url()->current() == route(auth()->user()->type.'.force-change-password')) {
            return $next($request);
        }

        return redirect()->route('client.welcome');
    }
}
