<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):((Response | RedirectResponse))  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if (session('myAdminId') || session('allDeloitteClient')) {
            return $next($request);
        }

        if (config('app.env') === 'production' && str_contains((string) auth()->user()->type, 'admin')) {
            // Google 2fa redirects
            if (url()->current() == route(auth()->user()->type.'.google2fa.create') || url()->current() == route(auth()->user()->type.'.google2fa.back')) {
                return $next($request);
            }

            if (str_contains((string) auth()->user()->type, 'admin') && empty(auth()->user()->google2fa_secret)) {
                return redirect()->route(auth()->user()->type.'.google2fa.create');
            }
            // Google 2fa redirects
        }

        if (config('app.env') === 'production' && auth()->user()->type != 'production_translating_admin' && (auth()->user()->type !== 'client' && ! request()->hasValidSignature())) {
            // Force change password redirects
            if (url()->current() == route(auth()->user()->type.'.force-change-password')) {
                return $next($request);
            }

            if (! empty(auth()->user()->connected_account)) {
                return $next($request);
            }

            // When user type is not (client, expert, operator), check if current url is not google2fa process before redirecting to force chnage password
            if (! in_array(auth()->user()->type, ['client', 'operator', 'expert'])) {
                if (url()->current() != route(auth()->user()->type.'.google2fa.process')
                && (empty(auth()->user()->password_changed_at) || Carbon::parse(auth()->user()->password_changed_at)->addDays(90)->isPast())) {
                    return redirect()->route(auth()->user()->type.'.force-change-password');
                }
            } elseif (empty(auth()->user()->password_changed_at) || Carbon::parse(auth()->user()->password_changed_at)->addDays(90)->isPast()) {
                return redirect()->route(auth()->user()->type.'.force-change-password');
            }
            // Force change password redirects
        }

        return $next($request);
    }
}
