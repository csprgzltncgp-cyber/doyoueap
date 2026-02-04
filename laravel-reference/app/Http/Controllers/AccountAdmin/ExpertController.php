<?php

namespace App\Http\Controllers\AccountAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\LanguageSkill;
use App\Models\Permission;
use App\Models\Specialization;
use App\Models\User;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;

class ExpertController extends Controller
{
    public function index()
    {
        $experts = User::query()->where('type', 'expert')->orderBy('name')->get();

        return view('account-admin.experts.index', ['experts' => $experts]);
    }

    public function show(User $user)
    {
        $countries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();
        $cities = City::query()->withoutGlobalScope(CountryScope::class)->orderBy('name')->get();
        $permissions = Permission::query()->get();
        $specializations = Specialization::query()->get();
        $languageSkills = LanguageSkill::query()->get();
        $user->load('expert_data');

        return view('account-admin.experts.show', ['user' => $user, 'countries' => $countries, 'cities' => $cities, 'permissions' => $permissions, 'specializations' => $specializations, 'languageSkills' => $languageSkills]);
    }

    public function filter()
    {
        $cities = City::query()->orderBy('name')->get();
        $permissions = Permission::query()->get();

        return view('account-admin.experts.filter', ['cities' => $cities, 'permissions' => $permissions]);
    }

    public function filter_result()
    {
        $filters = array_filter(request()->all());

        $builder = User::query()->where('type', 'expert');

        if (array_key_exists('name', $filters)) {
            $builder = $builder->where('name', 'like', '%'.$filters['name'].'%');
            unset($filters['name']);
        }

        if (array_key_exists('permission_id', $filters)) {
            $builder = $builder->whereHas('permission', function ($query) use ($filters): void {
                $query->where('permission_id', $filters['permission_id']);
            });
            unset($filters['permission_id']);
        }

        $users = $builder->get();

        if (array_key_exists('city_id', $filters)) {
            $users = $users->filter(fn (User $expert): bool => in_array($filters['city_id'], $expert->cities->pluck('id')->toArray()));
        }

        return view('account-admin.experts.result', ['users' => $users]);
    }
}
