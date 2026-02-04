<?php

namespace App\Http\Livewire\Admin\Data\IncomingOutgoingInvoices;

use App\Enums\ContractHolderEnum;
use App\Helpers\CurrencyCached;
use App\Models\Company;
use App\Models\Country;
use App\Models\DirectInvoice;
use App\Traits\DashboardData\IncomingOutgoingInvoices\ExtendedDataCollect;
use Carbon\Carbon;
use Livewire\Component;

class CgpData extends Component
{
    use ExtendedDataCollect;

    public $data;

    public $show_data = false;

    public $show_country = [];

    public $filter_year;

    public $filter_month = '';

    public $companies;

    public $countries;

    public $name = 'CGP Europe';

    public function mount(): void
    {
        $this->companies = Company::query()->pluck('id', 'name')->toArray();
        $this->countries = Country::query()->pluck('id', 'name')->toArray();

        $this->filter_year = Carbon::now()->format('Y');
    }

    public function render()
    {
        return view('livewire.admin.data.incoming-outgoing-invoices.extended-data');
    }

    public function updatedShowData($opened): void
    {
        if (! $opened) {
            return;
        }
        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::CGP),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month)
        );
    }

    public function updatedFilterYear(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::CGP),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month)
        );
    }

    public function updatedFilterMonth(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::CGP),
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

        $data = DirectInvoice::query()->with(['country', 'company', 'company.countries', 'company.direct_billing_datas'])->whereHas('company', function ($query): void {
            $query->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', ContractHolderEnum::CGP->value));
        })->get()->filter(fn ($record): bool => is_invoice_done($record));

        $data->each(function ($record) use (&$result, $converter): void {
            $from = Carbon::parse($record->from);
            $to = Carbon::parse($record->to);

            $total = get_invoice_net_total($record->data);

            $currency = strtoupper((string) $record->data['billing_data']['currency']);

            $converted_total = $converter->convert($total, 'EUR', $currency);

            if (! $country = $record->country) {
                $country = $record->company->countries->first();
            }

            $diff = $from->diffInMonths($to) + 1;

            $converted_total = round($converted_total / $diff, 2);

            for ($i = 0; $i < $diff; $i++) {
                $date = $from->addMonths($i)->format('Y-m');

                if (! isset($result['countries'][$country->id]['companies'][$record->company->id][$date])) {
                    $result['countries'][$country->id]['companies'][$record->company->id][$date] = $converted_total;
                } else {
                    $result['countries'][$country->id]['companies'][$record->company->id][$date] += $converted_total;
                }
            }
        });

        $filtered = $this->filter_datas($result, $filter_year, $filter_month);

        return $this->summarize_outgoing_datas($filtered);
    }
}
