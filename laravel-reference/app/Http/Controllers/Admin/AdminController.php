<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use App\Traits\ForceChangePasswordTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class AdminController extends Controller
{
    use ForceChangePasswordTrait;

    public function login()
    {
        (new Authenticator(request()))->logout();

        return view('admin.login');
    }

    public function register(Request $request)
    {
        $users = User::query()->where('password', null)->where('type', 'admin')->get();
        $user = $users->filter(fn ($value, $key) => Hash::check($value->id, $request->id));
        if ($user->count() === 0) {
            abort(403);
        }

        session(['user_id' => $user[0]->id]);

        return view('admin.admins.register');
    }

    public function register_process(Request $request)
    {
        if ($request->password != $request->password_confirmation) {
            return redirect()->back();
        }
        $user = User::query()->findOrFail(session('user_id'));

        $user->password = Hash::make($request->password);
        $user->save();

        $this->store_password_history($user);

        return redirect()->route('admin.login');
    }

    public function login_process(Request $request)
    {
        $user = User::query()->where('username', $request->username)->first();

        if (! $user || ! (
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'production_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'production_translating_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'account_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'financial_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'eap_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'todo_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'affiliate_search_admin', 'active' => 1]) ||
            Auth::attempt(['username' => $request->username, 'password' => $request->password, 'type' => 'supervisor_admin', 'active' => 1])
        )) {
            activity()
                ->causedBy($user)
                ->event('login_failed')
                ->log('login_failed');

            return redirect()->back()->withErrors('Invalid username or password.');
        }

        activity()
            ->causedBy($user)
            ->event('login')
            ->log('login');

        return redirect()->route(Auth::user()->type.'.dashboard');
    }

    public function dashboard()
    {
        $user_notifications = Auth::user()->getNotifications();

        return view('admin.dashboard', ['user_notifications' => $user_notifications]);
    }

    public function index()
    {
        $countries = Country::query()->get();
        $admins = User::query()->where('type', 'admin')
            ->orWhere('type', 'production_admin')
            ->orWhere('type', 'production_translating_admin')
            ->orWhere('type', 'account_admin')
            ->orWhere('type', 'financial_admin')
            ->orWhere('type', 'eap_admin')
            ->orWhere('type', 'todo_admin')
            ->orWhere('type', 'affiliate_search_admin')
            ->orWhere('type', 'supervisor_admin')
            ->orderBy('id', 'desc')->get();

        return view('admin.admins.list', ['admins' => $admins, 'countries' => $countries]);
    }

    public function create()
    {
        $countries = Country::query()->get();
        $languages = Language::query()->orderBy('name', 'asc')->get();
        $users = User::query()
            ->whereIn('type', ['admin', 'production_admin', 'todo_admin', 'affiliate_search_admin', 'production_translating_admin', 'account_admin', 'financial_admin', 'eap_admin', 'supervisor_admin'])
            ->where('connected_account', null)
            ->orderBy('username', 'asc')
            ->get();

        return view('admin.admins.new', ['countries' => $countries, 'languages' => $languages, 'users' => $users]);
    }

    public function store(Request $request)
    {
        if ($request->password != $request->password_confirmation) {
            return redirect()->back();
        }
        $user = User::store_admin($request->only(['type', 'name', 'email', 'username', 'country_id', 'language_id', 'password', 'connected_account']));
        $this->store_password_history($user);

        return redirect()->route('admin.admins.list');
    }

    public function edit($id)
    {
        $user = User::query()->findOrFail($id);
        $countries = Country::query()->get();
        $languages = Language::query()->orderBy('name', 'asc')->get();
        $users = User::query()
            ->whereIn('type', ['admin', 'production_admin', 'todo_admin', 'affiliate_search_admin', 'production_translating_admin', 'account_admin', 'financial_admin', 'eap_admin', 'supervisor_admin'])
            ->where('connected_account', null)
            ->orderBy('username', 'asc')->get();

        return view('admin.admins.edit', ['user' => $user, 'countries' => $countries, 'languages' => $languages, 'users' => $users]);
    }

    public function edit_process($id, Request $request)
    {
        User::edit_admin($id, $request->only(['type', 'name', 'email', 'username', 'country_id', 'language_id', 'connected_account']));

        return redirect()->route('admin.admins.edit', ['id' => $id]);
    }

    public function toggleActive(Request $request)
    {
        $user = User::query()->findOrFail($request->id);
        $user->toggleActive();

        return response()->json(['status' => 0, 'active' => $user->active]);
    }

    public function toggleLocked()
    {
        $user = User::query()->findOrFail(request()->id);
        $user->toggleLocked();

        return response()->json(['status' => 0, 'locked' => $user->locked]);
    }

    public function loginAs(Request $request)
    {
        if (! str_contains((string) Auth::user()->type, 'admin')) {
            return response()->json([
                'status' => 1,
                'message' => 'You are not authorized to login as another user.',
            ]);
        }

        session(['myAdminId' => Auth::user()->id]);
        session(['myAdminLastUrl' => url()->previous()]);
        Auth::loginUsingId($request->id);

        return response()->json([
            'status' => 0,
            'redirect' => '/'.$request->type.'/dashboard',
        ]);
    }

    public function loginBackAsAdmin()
    {
        Auth::loginUsingId(session('myAdminId'));
        session()->forget('myAdminId');
        session()->forget('masterCompanyAccountId');

        // Remove logged in client id from session
        if (session('originalClient')) {
            session()->forget('originalClient');
        }

        $url = session('myAdminLastUrl');
        session()->forget('myAdminLastUrl');

        return response()->json([
            'status' => 0,
            'redirect' => $url,
        ]);
    }

    /* DELETE */
    public function delete($user_id)
    {
        $user = User::query()->findOrFail($user_id);
        $user->delete();

        return response(['status' => 0]);
    }

    /* DELETE */
}
