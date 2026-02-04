<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AssetController extends Controller
{
    // Show asset and waste menu
    public function index()
    {
        return view('admin.assets.index');
    }
}
