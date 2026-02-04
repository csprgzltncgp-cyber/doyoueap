<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Language;
use App\Models\Notification;
use App\Models\NotificationSeen;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::query()->orderBy('id', 'desc')->with('seen')->get();

        return view('admin.notifications.list', ['notifications' => $notifications]);
    }

    public function create()
    {
        $languages = Language::query()->get();
        $countries = Country::query()->orderBy('code')->get();
        $users = User::query()->orderBy('name')->get();
        $permissions = Permission::query()->orderBy('slug', 'asc')->get();

        return view('admin.notifications.new', ['users' => $users, 'languages' => $languages, 'countries' => $countries, 'permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        Notification::createNotification($request->except('_token'));

        return redirect()->route('admin.notifications.list');
    }

    public function edit($id)
    {
        $languages = Language::query()->get();
        $users = User::query()->orderBy('name')->get();
        $notification = Notification::query()->findOrFail($id);
        $countries = Country::query()->orderBy('code')->get();
        $permissions = Permission::query()->orderBy('slug', 'asc')->get();

        return view('admin.notifications.edit', ['users' => $users, 'languages' => $languages, 'notification' => $notification, 'countries' => $countries, 'permissions' => $permissions]);
    }

    public function edit_process(Request $request, $id)
    {
        Notification::editNotification($request->except('_token'), $id);

        return redirect()->route('admin.notifications.edit', ['id' => $id]);
    }

    public function delete($id)
    {
        $notification = Notification::query()->findOrFail($id);
        $notification->delete();

        return response()->json(['status' => 0]);
    }

    public function notification_seen($id)
    {
        if (session()->get('myAdminId')) {
            return response()->json(['status' => 0]);
        }
        if ($id == 'always') {
            return response()->json(['status' => 0]);
        }
        NotificationSeen::query()->firstOrCreate(['user_id' => Auth::id(), 'notification_id' => $id]);

        return response()->json(['status' => 0]);
    }
}
