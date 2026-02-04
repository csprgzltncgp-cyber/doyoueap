<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class ContractHolderCompanyDataController extends Controller
{
    public function __invoke(Request $request)
    {
        $contract_holder_company = request('contract_holder_company');

        $company = Company::query()->withoutGlobalScopes()->where('id', (int) $contract_holder_company)->first();
        $countryDifferentiates = $company->country_differentiates()->firstOrCreate();

        return view('admin.invoice-helper.contract-holder-company-data.index', ['contract_holder_company' => $contract_holder_company, 'company' => $company, 'countryDifferentiates' => $countryDifferentiates]);
    }
}
