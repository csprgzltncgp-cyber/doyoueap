<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\EapUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EapUsersController extends Controller
{
    public function index()
    {
        $users = EapUser::with('company')->paginate(10);

        return view('admin.eap-online.users.list', ['users' => $users]);
    }

    public function filter_view()
    {
        $countries = Country::all();
        $companies = Company::query()->where('active', 1)->orderBy('name')->get();

        return view('admin.eap-online.users.filter', ['countries' => $countries, 'companies' => $companies]);
    }

    public function filter(Request $request)
    {
        $filters = array_filter($request->all());
        $query = EapUser::query();

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'username':
                    $query = $query->where('username', 'like', '%'.$value.'%');
                    break;
                case 'country':
                    $query = $query->where('country_id', $value);
                    break;
                case 'company':
                    $query = $query->where('company_id', $value);
                    break;
            }
        }

        if ($request->email) {
            $users = $query->cursor()->filter(fn (EapUser $user): bool => $user->email == $request->email);

            return view('admin.eap-online.users.result', ['users' => $users]);
        }

        $users = $query->get();

        return view('admin.eap-online.users.result', ['users' => $users]);
    }

    public function delete($id): ?array
    {
        try {
            EapUser::query()->findOrFail($id)->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return ['status' => 0];
    }
}
