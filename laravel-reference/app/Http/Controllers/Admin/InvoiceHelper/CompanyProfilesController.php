<?php

namespace App\Http\Controllers\Admin\InvoiceHelper;

use App\Http\Controllers\Controller;

class CompanyProfilesController extends Controller
{
    public function __invoke()
    {
        return view('admin.invoice-helper.company-profiles.index');
    }
}
