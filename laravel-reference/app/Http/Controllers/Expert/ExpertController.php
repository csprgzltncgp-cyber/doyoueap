<?php

namespace App\Http\Controllers\Expert;

use App\Mail\PasswordResetEmail;
use App\Models\User;
use App\Traits\ForceChangePasswordTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ExpertController extends BaseExpertController
{
    use ForceChangePasswordTrait;

    public function register(Request $request)
    {
        $users = User::query()->where('password', null)->where('type', 'expert')->get();
        $user = $users->filter(fn ($value, $key) => Hash::check($value->id, $request->id))->first();

        if ($user === null) {
            abort(403);
        }

        session(['user_id' => $user->id]);

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

        return redirect()->route('expert.login');
    }

    public function login()
    {
        return view('expert.login');
    }

    public function password_change()
    {
        return view('expert.password');
    }

    public function reset_password()
    {
        return view('expert.reset_password');
    }

    public function reset_password_process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'exists:users',
                'email',
                function ($attribute, $value, $fail): void {
                    $user = User::query()
                        ->where('email', '=', $value)
                        ->where('type', '=', 'expert')
                        ->where('contract_canceled', 0)
                        ->first();
                    if (! isset($user)) {
                        $fail('not expert');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect('expert/reset-password')
                ->withErrors($validator)
                ->withInput();
        }

        $new_password = Str::random();

        $user = User::query()->where('email', '=', $request->get('email'))->where('type', '=', 'expert')->first();
        $user->password = Hash::make($new_password);
        $user->save();

        Mail::to($user->email)->send(new PasswordResetEmail($user, $new_password));

        return redirect()->back()->with('message', 'ok');
    }

    public function login_process(Request $request)
    {
        $user = User::query()->where('username', $request->username)->where('type', 'expert')->first();

        if (! $user || ! Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'expert'])) {
            activity()
                ->causedBy($user)
                ->event('login_failed')
                ->log('login_failed');

            return redirect()->route('expert.login')->withErrors('Invalid username or password.');
        }

        // ha még nem lépett be, akkor küldünk neki gratuláló emailt
        if (! $user->last_login_at) {
            $user->sendAfterFirstLoginMail();
        }

        // felülírjük a legutolsó bejelentkezést
        User::query()->where('id', $user->id)->update([
            'last_login_at' => Carbon::now(),
        ]);

        // ha van ref, akkor oda irányítjuk
        if ($request->ref && $request->ref == 'case') {
            return redirect()->route('expert.cases.view', ['id' => $request->id]);
        }

        activity()
            ->causedBy($user)
            ->event('login')
            ->log('login');

        return redirect()->route('expert.dashboard');
    }

    public function dashboard()
    {
        return view('expert.dashboard');
    }
}
