<?php

namespace App\Http\Livewire\Admin\Data;

use App\Helpers\CurrencyCached;
use App\Models\InvoiceCaseData;
use App\Scopes\CountryScope;
use Carbon\Carbon;
use Livewire\Component;

class InvoicedConsultation extends Component
{
    public $datas;

    public $show_data;

    public $filter;

    public $year;

    public function render()
    {
        return view('livewire.admin.data.invoiced-consultation');
    }

    public function mount(): void
    {
        $this->show_data = false;
        $this->year = (int) (Carbon::now()->quarter == 1) !== 0 ? Carbon::now()->subYear()->format('Y') : Carbon::now()->format('Y');
    }

    public function show_data(): void
    {
        $this->show_data = ! $this->show_data;
        $this->get_data();
    }

    public function get_data(): void
    {
        $converter = new CurrencyCached(60 * 60 * 24);

        $start_date = Carbon::now()->setYear($this->year)->startOfYear()->addMonth()->format('Y-m-d');

        /* If select date if not the current year OR higher get the whole year data.
        Invoices/InvoiceCaseData applies to the previous month period, compared to the updated_at DATE.
        E.G : To Get invoices for 2022, we have to get data up to the next year, 2023-01-10. */
        $end_date = Carbon::now()->setYear($this->year)->addYear()->startOfYear()->addDays(10)->format('Y-m-d');

        if ($this->year >= (int) Carbon::now()->format('Y')) {
            // If current year or bigger, than end of invoicing period is: 10th day of the month at 00:00 hour
            $end_date = Carbon::now()->setYear($this->year)->startOfMonth()->addDays(10)->format('Y-m-d');
        }

        if ($this->year == 'all_years') {
            $this->datas = $this->get_invoice_case_data(
                Carbon::now()->setYear(2022)->startOfYear()->addMonth()->format('Y-m-d'),
                Carbon::now()->startOfMonth()->addDays(10)->format('Y-m-d')
            );

            $this->datas->map(function ($item): void {
                /* IF update_at is between (current_year)-01-01 AND (current_year)-01-10,
                than the invoice period applies to the last month of the previous year.
                Change the update_at year to the previous year so that the when grouping by year, WHEN showing
                "all_years" the invoice numbers count in the right year */
                if ($item->updated_at->between(Carbon::now()->startOfYear(), Carbon::now()->startOfYear()->addDays(10))) {
                    $item->updated_at = $item->updated_at->subYear();
                }
            });
        } else {
            $this->datas = $this->get_invoice_case_data($start_date, $end_date);
        }

        if ($this->filter == 'country') {
            // Affiliate invoice and consultation totals for complete quarters
            $this->datas = collect($this->datas->groupBy('invoice.country.code')
                ->map(fn ($country_case_datas, $country) => (object) [
                    'country' => $country,
                    'period' => $country_case_datas->groupBy(fn ($value) => ($this->year == 'all_years') ? $value->updated_at->format('Y') : $value->updated_at->format('Y-m'))
                        ->map(fn ($month_invoice_data, $period) => (object) [
                            'period_num' => ($this->year == 'all_years') ? $period : Carbon::parse($period)->subMonth()->month,
                            'count' => $month_invoice_data->sum('consultations_count'),
                            'amount' => round($month_invoice_data->unique('invoice_id')->reduce(function ($carry, $item) use ($converter) {

                                if (! $item->invoice || ! $item->invoice->currency) {
                                    return $carry;
                                }
                                $amount = filter_var(
                                    $item->invoice->getRawOriginal('grand_total'),
                                    FILTER_SANITIZE_NUMBER_FLOAT,
                                    FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND
                                );
                                $exchanged_amount = $converter->convert((float) $amount, 'EUR', strtoupper((string) $item->invoice->currency));

                                return $carry + $exchanged_amount;
                            }, 0), 2),
                        ]),
                ])
                ->values()->all());
        } else {

            // Affiliate invoice and consultation totals for complete quarters
            $this->datas = collect($this->datas->groupBy(
                fn ($value) => ($this->year == 'all_years') ? $value->updated_at->format('Y') : $value->updated_at->format('Y-m')
            )->map(fn ($invoice_case_datas, $period) => (object) [
                'period_num' => ($this->year == 'all_years') ? $period : Carbon::parse($period)->subMonth()->month,
                'count' => $invoice_case_datas->sum('consultations_count'),
                'amount' => round($invoice_case_datas->unique('invoice_id')->reduce(function ($carry, $item) use ($converter) {

                    if (! $item->invoice || ! $item->invoice->currency) {
                        return $carry;
                    }

                    $amount = filter_var(
                        $item->invoice->getRawOriginal('grand_total'),
                        FILTER_SANITIZE_NUMBER_FLOAT,
                        FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND
                    );
                    $exchanged_amount = $converter->convert((float) $amount, 'EUR', strtoupper((string) $item->invoice->currency));

                    return $carry + $exchanged_amount;
                }, 0), 2),
            ])->values()->all());
        }
    }

    public function get_invoice_case_data($start_date, $end_date)
    {
        return InvoiceCaseData::query()
            ->withoutGlobalScope(CountryScope::class)
            ->has('invoice')
            ->with(['invoice', 'invoice.country'])
            ->orderBy('updated_at')
            ->whereBetween('updated_at', [
                $start_date,
                $end_date,
            ])
            ->get();
    }
}
