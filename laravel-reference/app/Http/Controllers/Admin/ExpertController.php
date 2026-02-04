<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\EapOnline\EapOnlineTherapyAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExpertController extends Controller
{
    public function index()
    {
        $countries = Country::query()->orderBy('code', 'asc')->get();
        $experts = User::query()->where('type', 'expert')->with('expertCountries', 'expertCrisisCountries', 'inactivity')->orderBy('name', 'asc')->get();
        $activeExperts = User::query()->where('type', 'expert')->whereNotNull('password')->orderBy('email', 'asc')->select('email', 'country_id')->get();

        return view('admin.experts.list', ['experts' => $experts, 'countries' => $countries, 'activeExperts' => $activeExperts]);
    }

    public function sendBatchRegMail($id)
    {
        $users = User::query()->where('last_login_at', null)->where('country_id', $id)->get();

        foreach ($users as $user) {
            $user->sendCreatedMail();
        }

        return response()->json(['status' => 0, 'users' => $users]);
    }

    public function delete($id)
    {
        User::query()->where('id', $id)->update([
            'email' => 'DELETED',
            'username' => 'DELETED',
        ]);
        User::query()->where('id', $id)->where('type', 'expert')->delete();

        EapOnlineTherapyAppointment::query()->where('expert_id', $id)->delete();

        return response()->json(['status' => 0]);
    }

    public function resendRegistrationEmail(Request $request)
    {
        User::resendRegMail($request->id);

        return response()->json(['status' => 0]);
    }

    public function expertGenerateCountries()
    {
        $experts = User::query()->where('type', 'expert')->with('expertCountries')->get();
        foreach ($experts as $expert) {
            if (! $expert->expertCountries->count()) {
                $expert->expertCountries()->sync([$expert->country_id]);
            }
        }

        return response('Done', 200);
    }

    public function changeExpertCountry(Request $request)
    {
        auth()->user()->update([
            'country_id' => $request->get('id'),
        ]);

        return response()->json([
            'status' => 0,
        ]);
    }

    public function cancel_contract(User $expert): bool
    {
        $expert->locked = true;
        $expert->active = false;
        $expert->contract_canceled = true;
        $expert->password = Hash::make(Str::random(8));
        $expert->save();

        return true;
    }
}
