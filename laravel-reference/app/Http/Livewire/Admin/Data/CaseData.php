<?php

namespace App\Http\Livewire\Admin\Data;

use App\Enums\DashboardDataType;
use App\Models\DashboardData;
use App\Traits\EapOnline\Riport;
use Carbon\Carbon;
use Livewire\Component;

class CaseData extends Component
{
    use Riport;

    public $contract_holder_data;

    public $date_intervals;

    public $country_case_data;

    public $show_data = [];

    public $month;

    public $filter;

    public function mount(): void
    {
        $this->date_intervals = [
            'from' => Carbon::now()->now()->subYearNoOverflow()->startOfMonth()->format('Y.m.d'),
            'to' => Carbon::now()->subMonth()->endOfMonth()->format('Y.m.d'),
        ];
    }

    public function render()
    {
        return view('livewire.admin.data.case-data');
    }

    public function show_data($item): void
    {
        // Set array containing which elements are open/visible
        if ($this->show_data && in_array($item, $this->show_data)) {
            if (($key = array_search($item, $this->show_data)) !== false) {
                unset($this->show_data[$key]);
            }
        } else {
            $this->show_data[] = $item;
        }
    }

    public function get_data($name): void
    {
        if ($name == 'contract_holder') {
            DashboardData::query()->where('type', DashboardDataType::TYPE_CONTRACT_HOLDER_DATA->value)->get()->each(function ($item, $key): void {
                $this->contract_holder_data[array_key_first($item->data)] = $item->data[array_key_first($item->data)];
            });
            $this->show_data('contract_holder');
        }

        if ($name == 'countries') {
            $this->country_case_data = optional(DashboardData::query()->where('type', DashboardDataType::TYPE_COUNTRY_CASE_DATA->value)->first())->data;

            if ($this->filter != 'months') {
                // Sum month data (TOTAL)
                $sum_data = [];
                $this->country_case_data = collect($this->country_case_data)->each(function (array $country_data, $country) use (&$sum_data): void {
                    $sum_data[$country] = [];
                    collect(array_values($country_data['cases']))->each(function ($item) use (&$sum_data, $country): void {
                        $sum_data[$country][] = $item;
                    });
                    $sum_data[$country] = $this->merge_and_sum_riport_values($sum_data[$country]);
                });

                $this->country_case_data = $sum_data;
            }
            ksort($this->country_case_data);
            $this->show_data('countries');
        }
    }
}
