<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class Google2FaController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $google2fa = app(Google2FA::class);

        $secret = $google2fa->generateSecretKey();

        $qr_code_url = $google2fa->getQRCodeUrl(
            Str::of($user->type)->replace('_', ' ')->title().' '.config('app.name'),
            $user->email,
            $secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd
            )
        );

        $qr_image = base64_encode($writer->writeString($qr_code_url));

        return view('google2fa.create', ['qr_image' => $qr_image, 'secret' => $secret]);
    }

    public function store()
    {
        $user = Auth::user();
        $user->google2fa_secret = request()->input('secret');
        $user->save();

        session()->flash('2fa_setup', true);

        return redirect(route($user->type.'.dashboard'));
    }

    public function process()
    {
        $user = Auth::user();

        return redirect(route($user->type.'.dashboard'));
    }

    public function back()
    {
        $user = Auth::user();
        $user->google2fa_secret = null;
        $user->save();

        return redirect()->route($user->type.'.google2fa.create');
    }
}
