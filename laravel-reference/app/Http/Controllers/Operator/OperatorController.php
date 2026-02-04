<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ForceChangePasswordTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    use ForceChangePasswordTrait;

    public function register(Request $request)
    {
        $users = User::query()->where('password', null)->where('type', 'operator')->get();
        $user = $users->filter(fn ($value, $key) => Hash::check($value->id, $request->id));

        if ($user->count() === 0) {
            abort(403);
        }

        session(['user_id' => $user[0]->id]);

        return view('expert.experts.register');
    }

    public function register_process(Request $request)
    {
        if ($request->password != $request->password_confirmation) {
            return redirect()->back();
        }
        $user = User::query()->findOrFail(session('user_id'));
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('operator.login');
    }

    public function login()
    {
        return view('operator.login');
    }

    public function login_process(Request $request)
    {
        $user = User::query()->where('username', $request->username)->where('type', 'operator')->first();

        if (! $user || ! Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'operator', 'active' => 1])) {
            activity()
                ->causedBy($user)
                ->event('login_failed')
                ->log('login_failed');

            return redirect()->route('operator.login')->withErrors('Invalid username or password.');
        }

        activity()
            ->causedBy($user)
            ->event('login')
            ->log('login');

        return redirect()->route('operator.dashboard');
    }

    public function loginAsOtherAccount(Request $request)
    {
        if (Auth::user()->connected_account == $request->id ||
            User::query()->where('id', $request->id)->where('connected_account', Auth::user()->id)->count() ||
            User::query()->where('id', $request->id)->where('connected_account', Auth::user()->connected_account)->whereNotNull('connected_account')->count()
        ) {
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

    public function dashboard()
    {
        $user_notifications = Auth::user()->getNotifications();

        return view('operator.dashboard', ['user_notifications' => $user_notifications]);
    }
}
