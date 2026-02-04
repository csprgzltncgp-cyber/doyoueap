<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ForceChangePasswordTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    use ForceChangePasswordTrait;

    public function login()
    {
        /**
         * Check if link is signed and contain the "volume_request" page param. If yes, automatical log in the user based on the client_id param.
         * Otherwise return login page view
         */
        if (request()->hasValidSignature() && request('page') == 'volume_request') {

            // If a user is already logged in log them out to avoid redirect loop.
            if (Auth::user() !== null) {
                Auth::logout();
            }

            if (User::query()->find(request('client_id'))) {
                Auth::loginUsingId(request('client_id'));

                return redirect()->route('client.volume-request', ['month' => request('month')]);
            }
        }

        return view('client.login');
    }

    public function login_as_deloitte_client()
    {
        request()->validate([
            'id' => 'required',
            'path' => 'required',
        ]);

        Auth::loginUsingId(request()->id);

        return response()->json([
            'status' => 0,
            'redirect' => request()->path,
        ]);
    }

    public function loginAsOtherAccount(Request $request)
    {
        $accounts = Auth::user()->hasConnectedClientAccounts();
        if (in_array($request->id, $accounts->pluck('id')->toArray())) {
            Auth::loginUsingId($request->id);

            return response()->json([
                'status' => 0,
                'redirect' => '/'.$request->type.'/dashboard',
            ]);
        }

        return response()->json([
            'status' => 1,
        ]);
    }

    public function login_process(Request $request)
    {
        $user = User::query()->where('username', $request->username)->where('type', 'client')->first();

        if (! $user || ! Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'client', 'active' => 1])) {
            activity()
                ->causedBy($user)
                ->event('login_failed')
                ->log('login_failed');

            return redirect()->route('client.login')->withErrors([
                'username' => 'Invalid username or password.',
            ]);
        }

        // Store company user ID in session if the current company serves as a master for others
        $company = auth()->user()->companies()->first();
        if ($company->is_master_company()) {
            session(['masterCompanyAccountId' => auth()->user()->id]);
        }

        activity()
            ->causedBy($user)
            ->event('login')
            ->log('login');

        $user->email_verified_at = now();
        $user->save();

        // deloitte hungary clinet can access to all the deloitte clients
        if (auth()->id() == 687) {
            session()->put('allDeloitteClient', true);
        }

        activity()
            ->causedBy($user)
            ->event('login')
            ->log('login');

        return redirect()->route('client.riport.show', ['totalView' => 1]);
    }

    public function new_password()
    {
        return view('client.new-password');
    }

    public function custom_language($code)
    {
        session()->put('client-language', $code);
        Carbon::setLocale($code);

        return redirect()->back();
    }
}
