<?php

namespace App\Http\Controllers\Admin\InvoiceHelper;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\DB;

class CompletionCertificateController extends Controller
{
    public function all()
    {
        $min_date = Carbon::parse('2023-01-01');
        $max_date = Carbon::parse(DB::table('direct_invoices')->max('to'));
        $dates = CarbonPeriod::create($min_date, '1 month', $max_date->format('Y-m-d'));

        return view('admin.invoice-helper.completion-certificate.all', ['dates' => $dates]);
    }

    public function download_all($date)
    {
        // if (all_completion_certificates_printed_in_month($date)) {
        //     return redirect()->back();
        // }

        $companies = Company::query()
            ->with(['country_differentiates', 'countries'])
            ->whereHas('direct_invoices', fn ($query) => $query
                ->whereDate('to', Carbon::parse($date)->endofMonth()->format('Y-m-d'))
                ->has('completion_certificate'))
            ->orderBy('name', 'asc')
            ->get();

        $merger = new Merger;

        foreach ($companies as $company) {
            $direct_invoices = $company->direct_invoices()
                ->whereDate('to', Carbon::parse($date)->endofMonth()->format('Y-m-d'))
                ->has('completion_certificate')
                ->get();

            foreach ($direct_invoices as $direct_invoice) {
                if (! array_key_exists('data', $direct_invoice->getAttributes())) {
                    continue;
                }

                $completion_certificate = $direct_invoice->completion_certificate;

                if (! $completion_certificate) {
                    continue;
                }

                $completion_certificate->printed_at = now();
                $completion_certificate->save();

                $html = view('admin.invoice-helper.completion-certificate.document', [
                    'with_header' => false,
                    'company' => $company,
                    'direct_invoice' => $direct_invoice,
                    'language' => strtolower($direct_invoice->data['billing_data']['invoice_language'] ?? 'en'),
                ])->render();

                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait')->output();

                $merger->addRaw($pdf);
            }
        }

        $pdf = $merger->merge();

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf;
            },
            'completion_certificates.pdf',
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    public function companies()
    {
        $year = request('year') ?? Carbon::now()->subMonthNoOverflow()->year;

        $min_date = Carbon::now()->setYear($year)->startOfYear();
        $max_date = (Carbon::now()->year === (int) $year) ? Carbon::now()->setYear($year)->subMonth()->endOfMonth() : Carbon::now()->setYear($year)->endOfYear();

        $dates = CarbonPeriod::create($min_date, '1 month', $max_date->format('Y-m-d'));

        $invoicing_years = array_reverse(CarbonPeriod::create(Carbon::parse('2023-01-01'), '1 year', Carbon::now()->subMonthNoOverflow()->format('Y-m-d'))->toArray());

        return view('admin.invoice-helper.completion-certificate.companies', ['dates' => $dates, 'invoicing_years' => $invoicing_years, 'selected_year' => $year]);
    }
}
