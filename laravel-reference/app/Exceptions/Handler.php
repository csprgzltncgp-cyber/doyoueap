<?php

namespace App\Exceptions;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelIgnition\Exceptions\ViewException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ViewException $e, Request $request) {
            if (session()->get('myAdminId')) {
                Auth::loginUsingId(session()->get('myAdminId'));
                session()->forget('myAdminId');

                if (session()->has('originalClient')) {
                    session()->forget('originalClient');
                }

                if (session()->has('myAdminLastUrl')) {
                    session()->forget('myAdminLastUrl');
                }

                return redirect()->to($request->url());
            }

            // Redirect user to the home/dashboard page
            return redirect()->route(auth()->user()->type.'.dashboard');
        });
    }
}
