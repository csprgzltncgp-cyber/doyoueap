<?php

namespace App\Http\Livewire\Admin\Data\IncomingOutgoingInvoices;

use App\Enums\ContractHolderCompany;
use App\Enums\ContractHolderEnum;
use App\Enums\DashboardDataType;
use App\Helpers\CurrencyCached;
use App\Models\Company;
use App\Models\Country;
use App\Models\DashboardData;
use App\Models\DirectInvoice;
use App\Traits\DashboardData\IncomingOutgoingInvoices\ExtendedDataCollect;
use Carbon\Carbon;
use Livewire\Component;

class LifeworksData extends Component
{
    use ExtendedDataCollect;

    public $data;

    public $show_data = false;

    public $show_country = [];

    public $filter_year;

    public $filter_month = '';

    public function mount(): void
    {
        $this->filter_year = Carbon::now()->format('Y');
    }

    public function render()
    {
        $companies = Company::query()->pluck('id', 'name')->toArray();
        $countries = Country::query()->pluck('id', 'name')->toArray();
        $name = 'Lifeworks';

        return view('livewire.admin.data.incoming-outgoing-invoices.extended-data', ['companies' => $companies, 'countries' => $countries, 'name' => $name]);
    }

    public function updatedShowData($opened): void
    {
        if (! $opened) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::LIFEWORKS),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month)
        );
    }

    public function updatedFilterYear(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::LIFEWORKS),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month)
        );
    }

    public function updatedFilterMonth(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::LIFEWORKS),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month)
        );
    }

    public function update_show_countries($country): void
    {
        if (in_array($country, $this->show_country)) {
            $this->show_country = array_diff($this->show_country, [$country]);
        } else {
            $this->show_country[] = $country;
        }
    }

    private function get_outgoing_datas(string $filter_year, string $filter_month): array
    {
        $result = [];
        $converter = new CurrencyCached(60 * 60 * 24);

        DashboardData::query()->where('type', DashboardDataType::TYPE_LIFEWORKS_DATA)->get()
            ->each(function ($record) use (&$result): void {
                $date = Carbon::parse($record->data['from'])->format('Y-m');

                if (! isset($result['countries'][$record->data['country']]['companies'][$record->data['company']][$date])) {
                    $result['countries'][$record->data['country']]['companies'][$record->data['company']][$date] = $record->data['amount'];
                } else {
                    $result['countries'][$record->data['country']]['companies'][$record->data['company']][$date] += $record->data['amount'];
                }

            });

        $filteted = $this->filter_datas($result, $filter_year, $filter_month);

        $summarized = $this->summarize_outgoing_datas($filteted);

        // change the total calculated from excel to the total calculated from the direct invoices
        $summarized['total'] = DirectInvoice::query()
            ->whereNotNull('invoice_number')
            ->whereHas('company', function ($query): void {
                $query->where('id', ContractHolderCompany::LIFEWORKS->value);
            })
            ->get()->filter(function (DirectInvoice $record) use ($filter_year, $filter_month): bool {
                $date = Carbon::parse($record->from)->format('Y-m');

                if ($filter_year !== '' && $filter_month === '') {
                    return Carbon::parse($date)->format('Y') == $filter_year;
                }
                if ($filter_year === '' && $filter_month !== '') {
                    return Carbon::parse($date)->format('m') == $filter_month;
                }
                if ($filter_year === '' && $filter_month === '') {
                    return true;
                }

                return Carbon::parse($date)->format('Y') == $filter_year && Carbon::parse($date)->format('m') == $filter_month;

            })->sum(function (DirectInvoice $record) use ($converter): float {
                $total = get_invoice_net_total($record->data);
                $currency = strtoupper((string) $record->data['billing_data']['currency']);

                $converted_total = $converter->convert($total, 'EUR', $currency);

                return round($converted_total, 2);
            });

        return $summarized;
    }
}
