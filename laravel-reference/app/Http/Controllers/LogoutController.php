<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class LogoutController extends Controller
{
    public function __invoke()
    {
        if (Auth::user() === null) {
            return redirect()->route('admin.login');
        }
        if (Auth::user()->type == 'admin') {
            session()->forget('myAdminId');
            (new Authenticator(request()))->logout();
        }
        $type = Auth::user()->type;

        if ($type == 'expert' && optional(Auth::user()->expert_data)->required_documents) {
            Auth::user()->expert_data->update(['completed_first' => false]);
        }

        Auth::logout();

        session()->forget('client-language');
        session()->forget('allDeloitteClient');
        session()->forget('originalClient');
        session()->forget('masterCompanyAccountId');

        return redirect()->route($type.'.login');
    }
}
