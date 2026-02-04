<?php

namespace App\Http\Livewire\Admin\Data;

use App\Models\User;
use App\Traits\TotalRiport;
use Carbon\Carbon;
use Livewire\Component;

class AffiliateNumbers extends Component
{
    use TotalRiport;

    public $affiliate_totals;

    public $consultation_totals;

    public $hourly_rates;

    public $invoices;

    public $consultations;

    public $affiliates;

    public $filter;

    public $year;

    public $month;

    public $countries;

    public $quarter_text;

    public $show_data;

    public $affiliate_total_count;

    public $consultation_total_count;

    public $horuly_rate_total_count;

    public function render()
    {
        return view('livewire.admin.data.affiliate-numbers');
    }

    public function mount(): void
    {
        // AFFILIATE DATA
        $this->affiliates = User::query()
            ->with('expert_data')
            ->has('invoices')
            ->whereHas('expert_data', function ($query): void {
                $query->where('is_cgp_employee', 0);
            })
            ->orderBy('name')
            ->get();

        $this->year = (int) (Carbon::now()->quarter == 1) !== 0 ? Carbon::now()->subYear()->format('Y') : Carbon::now()->format('Y');

        $this->show_data = false;

        $this->set_quarter_text();
    }

    public function get_data(): void
    {
        // Affiliate invoice and consultation totals for complete quarters
        $this->invoices = cache()->remember('affiliate_numbers_'.$this->year.'-'.$this->month, Carbon::now()->diffInSeconds(Carbon::now()->endOfQuarter()), fn () => $this->affiliates->map(function ($affiliate) {
            $results = $this->get_affiliate_invoice_numbers($affiliate->id, $this->year, $this->month);

            return (object) [
                'name' => $affiliate->name,
                'country' => $results['country'],
                'grand_total' => $results['invoice_totals'],
                'total_consultations' => $results['consultation_total'],
                'hourly_rate' => $results['hourly_rate'],
            ];
        }));

        $this->invoices = collect($this->invoices)->filter(fn ($item): bool => $item->total_consultations && $item->hourly_rate);

        if ($this->filter == 'country') {
            $this->affiliate_totals = collect($this->invoices)->groupBy('country')->values()->all();
            $this->consultation_totals = collect($this->invoices)->groupBy('country')->sortByDesc('total_consultations')->values()->all();
            $this->hourly_rates = collect($this->invoices)->groupBy('country')->sortBy('hourly_rate')->values()->all();

            // Total counts
            $this->affiliate_total_count = collect($this->affiliate_totals)->map(fn ($item) => collect([
                'country' => collect($item)->first()->country,
                'sum' => collect($item)->sum('grand_total'),
            ]));

            $this->consultation_total_count = collect($this->consultation_totals)->map(fn ($item) => collect([
                'country' => collect($item)->first()->country,
                'sum' => collect($item)->sum('total_consultations'),
            ]));

            $this->horuly_rate_total_count = collect($this->hourly_rates)->map(fn ($item) => collect([
                'country' => collect($item)->first()->country,
                'sum' => collect($item)->sum('hourly_rate'),
            ]));
        } else {
            $this->affiliate_totals = collect($this->invoices)->sortByDesc('grand_total')->values()->all();
            $this->consultation_totals = collect($this->invoices)->sortByDesc('total_consultations')->values()->all();
            $this->hourly_rates = collect($this->invoices)->sortBy('hourly_rate')->values()->all();

            // Total counts
            $this->affiliate_total_count = collect($this->affiliate_totals)->sum('grand_total');
            $this->consultation_total_count = collect($this->consultation_totals)->sum('total_consultations');
            $this->horuly_rate_total_count = collect($this->hourly_rates)->sum('hourly_rate');
        }

        $this->set_quarter_text();
    }

    public function show_data(): void
    {
        $this->show_data = ! $this->show_data;
        $this->get_data();
    }

    public function set_quarter_text(): void
    {
        $this->quarter_text = '';

        if ($this->month) {
            $this->quarter_text = $this->year.'. '.__('data.months_array')[$this->month - 1];

            return;
        }

        if ($this->year < Carbon::now()->format('Y')) {
            $this->quarter_text = 'Q1-Q4';
        } elseif ($this->year > Carbon::now()->format('Y')) {
            $this->quarter_text = __('data.no_affiliate_data');
        } else {
            switch (Carbon::now()->quarter) {
                case 1:
                    $this->quarter_text = __('data.no_affiliate_data');
                    break;

                case 2:
                    $this->quarter_text = 'Q1';
                    break;

                case 3:
                    $this->quarter_text = 'Q1-Q2';
                    break;

                case 4:
                    $this->quarter_text = 'Q1-Q3';
                    break;
            }
        }
    }
}
