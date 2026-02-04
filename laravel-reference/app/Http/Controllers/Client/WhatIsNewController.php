<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WhatIsNewController extends Controller
{
    public function select_language_view()
    {
        return view('client.whats-is-new.language-select');
    }

    public function select_language_process(string $language_code)
    {
        session()->put('client-language', $language_code);
        Carbon::setLocale($language_code);

        $has_cookie = (bool) Cookie::get('what-is-new-digital-balance');

        if (! $has_cookie) {
            return redirect()->route('client.what-is-new.video', ['language_code' => $language_code]);
        }

        if (Auth::check()) {
            return redirect()->route('client.riport.show', ['totalView' => 1]);
        }

        return redirect()->route('client.login', ['lang' => $language_code]);

    }

    public function video(string $language_code)
    {
        Cookie::queue('what-is-new-digital-balance', 'true', 60 * 24 * 365);

        return view('client.whats-is-new.video', ['language_code' => $language_code]);
    }

    public function contact(string $language_code)
    {
        $email = match ($language_code) {
            'hu' => 'barbara.kiss@cgpeu.com',
            'en' => 'peter.janky@cgpeu.com',
            'pl' => 'ewa.furmaniak@cgpeu.com',
            'ro' => 'alis.virtopeanu@cgpeu.com',
            default => 'barbara.kiss@cgpeu.com',
        };

        return view('client.whats-is-new.contact', ['language_code' => $language_code, 'email' => $email]);
    }
}
