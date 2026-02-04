<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    //
    /* INDEX */
    public function index()
    {
        $cities = City::query()->orderBy('name', 'asc')->get();
        $countries = Country::query()->get();

        return view('admin.cities.list', ['cities' => $cities, 'countries' => $countries]);
    }
    /* INDEX */

    /* EDIT */
    public function edit($id)
    {
        $city = City::query()->findOrFail($id);
        $countries = Country::query()->get();

        return view('admin.cities.edit', ['city' => $city, 'countries' => $countries]);
    }

    public function edit_process(Request $request, $id)
    {
        City::edit($id, $request);

        return redirect()->route(Auth::user()->type.'.cities.edit', ['id' => $id]);
    }
    /* EDIT */

    /* NEW */
    public function create()
    {
        $countries = Country::query()->get();

        return view('admin.cities.new', ['countries' => $countries]);
    }

    public function store(Request $request)
    {
        City::add($request);

        return redirect()->route(Auth::user()->type.'.cities.list');
    }
    /* NEW */

    /* DELETE */
    public function delete($city_id)
    {
        $city = City::query()->findOrFail($city_id);
        $city->delete();

        return response(['status' => 0]);
    }
    /* DELETE */
}
