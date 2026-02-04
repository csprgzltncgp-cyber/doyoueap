<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Redirector;
use Symfony\Component\HttpFoundation\Response;

class UserNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response|Redirector
    {
        if (Auth::check()) {
            $user_notifications = Auth::user()->getNotifications();
            view()->share('user_notifications', $user_notifications);
        }

        return $next($request);
    }
}
