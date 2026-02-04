<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() !== null) {
            App::setLocale(Auth::user()->language->getRawOriginal('code'));

            if (session()->has('client-language') && Auth::user()->type === 'client' && Arr::exists(config('client-languages'), session()->get('client-language'))) {
                App::setLocale(session()->get('client-language'));
            }
        } elseif ($request->lang) {
            App::setLocale($request->lang);

            return $next($request)->withCookie(cookie()->forever('lang', $request->lang));
        } elseif (Cookie::has('lang')) {
            App::setLocale(Cookie::get('lang'));
        }

        return $next($request);
    }
}
