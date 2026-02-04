<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;

class OperatorController extends Controller
{
    public function index()
    {
        $countries = Country::query()->orderBy('code', 'asc')->get();
        $operators = User::query()->where('type', 'operator')->orderBy('name', 'asc')->get();

        return view('admin.operators.list', ['operators' => $operators, 'countries' => $countries]);
    }

    public function delete($id)
    {
        $operator = User::query()->findOrFail($id);
        $operator->delete();

        return response(['status' => 0]);
    }
}
