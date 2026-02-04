<?php

namespace App\Http\Controllers\Admin\InvoiceHelper;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\DB;

class EnvelopeController extends Controller
{
    public function all()
    {
        $min_date = Carbon::parse('2023-01-01');
        $max_date = Carbon::parse(DB::table('direct_invoices')->max('to'));
        $dates = CarbonPeriod::create($min_date, '1 month', $max_date->format('Y-m-d'));

        return view('admin.invoice-helper.envelope.all', ['dates' => $dates]);
    }

    public function download_all($date)
    {
        // if (all_enevelopes_printed_in_month($date)) {
        //     return redirect()->back();
        // }

        $companies = Company::query()
            ->with(['country_differentiates', 'countries'])
            ->whereHas('direct_invoices', fn ($query) => $query
                ->whereDate('to', Carbon::parse($date)->endofMonth()->format('Y-m-d'))
                ->has('envelope'))
            ->orderBy('name', 'asc')
            ->get();

        $merger = new Merger;

        foreach ($companies as $company) {
            $direct_invoices = $company->direct_invoices()
                ->whereDate('to', Carbon::parse($date)->endofMonth()->format('Y-m-d'))
                ->has('envelope')
                ->get();

            foreach ($direct_invoices as $direct_invoice) {
                if (! array_key_exists('data', $direct_invoice->getAttributes())) {
                    continue;
                }

                $envelope = $direct_invoice->envelope;

                if (! $envelope) {
                    continue;
                }

                $envelope->printed_at = now();
                $envelope->save();

                $html = view('admin.invoice-helper.envelope.document', [
                    'data' => $direct_invoice->data,
                ])->render();

                $pdf = Pdf::loadHTML($html)->setPaper([0, 0, 595.28, 311.81], 'portrait')->output();

                $merger->addRaw($pdf);
            }
        }

        $pdf = $merger->merge();

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf;
            },
            'envelopes.pdf',
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

        return view('admin.invoice-helper.envelope.companies', ['dates' => $dates, 'invoicing_years' => $invoicing_years, 'selected_year' => $year]);
    }
}
