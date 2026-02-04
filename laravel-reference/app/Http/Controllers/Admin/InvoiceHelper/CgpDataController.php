<?php

namespace App\Http\Controllers\Admin\InvoiceHelper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CgpDataController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('admin.invoice-helper.cgp-data.index');
    }
}
