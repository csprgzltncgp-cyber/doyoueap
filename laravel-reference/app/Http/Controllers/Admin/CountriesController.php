<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CountriesController extends Controller
{
    public function index()
    {
        $countries = Country::query()->get();

        return view('admin.countries.index', ['countries' => $countries]);
    }

    public function show(Country $country)
    {
        return view('admin.countries.show', ['country' => $country]);
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store()
    {
        request()->validate([
            'name' => ['required'],
            'code' => ['required', 'max:2', 'unique:countries,code'],
            'email' => ['required'],
            'timezone' => ['required'],
        ]);

        $country = new Country;

        $country->code = Str::lower(request()->input('code'));
        $country->timezone = request()->input('timezone');
        $country->email = request()->input('email');
        $country->name = request()->input('name');
        $country->save();

        return redirect()->route(Auth::user()->type.'.countries.index');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', ['country' => $country]);
    }

    public function update(Country $country)
    {
        request()->validate([
            'name' => ['string'],
            'code' => ['string', 'max:2', 'unique:countries,code,'.$country->id],
            'email' => ['email'],
            'timezone' => ['string'],
        ]);

        $country->update([
            'code' => Str::lower(request()->input('code')),
            'timezone' => request()->input('timezone'),
            'email' => request()->input('email'),
            'name' => request()->input('name'),
        ]);

        return redirect()->route(Auth::user()->type.'.countries.index');
    }

    public function delete(Country $country)
    {
        if ($country->experts()->count() > 0 || $country->companies()->count() > 0 || $country->cities()->count() > 0) {
            return redirect()->route(Auth::user()->type.'.countries.index');
        }

        $country->delete();

        return redirect()->route(Auth::user()->type.'.countries.index');
    }
}
