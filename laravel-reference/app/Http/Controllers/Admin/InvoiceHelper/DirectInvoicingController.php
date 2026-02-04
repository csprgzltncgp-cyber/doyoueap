<?php

namespace App\Http\Controllers\Admin\InvoiceHelper;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DirectInvoicingController extends Controller
{
    public function __invoke()
    {
        $contract_holder_company = request('contract_holder_company');
        $year = request('year') ?? Carbon::now()->subMonthNoOverflow()->year;

        $invoicing_years = array_reverse(CarbonPeriod::create(Carbon::parse('2023-01-01'), '1 year', Carbon::now()->subMonthNoOverflow()->format('Y-m-d'))->toArray());

        $min_date = Carbon::now()->setYear($year)->startOfYear();
        $max_date = (Carbon::now()->year === (int) $year) ? Carbon::now()->setYear($year)->subMonth()->endOfMonth() : Carbon::now()->setYear($year)->endOfYear();

        $dates = CarbonPeriod::create($min_date, '1 month', $max_date->format('Y-m-d'));

        return view('admin.invoice-helper.direct-invoicing.index', [
            'dates' => $dates,
            'contract_holder_company' => $contract_holder_company,
            'invoicing_years' => $invoicing_years,
            'selected_year' => $year,
        ]);
    }
}
