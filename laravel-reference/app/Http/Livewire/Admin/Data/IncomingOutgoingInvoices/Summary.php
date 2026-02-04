<?php

namespace App\Http\Livewire\Admin\Data\IncomingOutgoingInvoices;

use App\Enums\DashboardDataType;
use App\Helpers\CurrencyCached;
use App\Models\DashboardData;
use App\Models\DirectInvoice;
use Carbon\Carbon;
use Livewire\Component;

class Summary extends Component
{
    public $show_data = false;

    public $filter_year;

    public $filter_month;

    public $data;

    public function mount(): void
    {
        $this->filter_year = Carbon::now()->format('Y');
        $this->filter_month = Carbon::now()->subMonthsNoOverflow()->format('m');
    }

    public function render()
    {
        return view('livewire.admin.data.incoming-outgoing-invoices.summary');
    }

    public function updatedShowData($opened): void
    {
        if (! $opened) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_summary($this->filter_year, $this->filter_month),
            $this->get_outgoing_summary($this->filter_year, $this->filter_month)
        );
    }

    public function updatedFilterYear(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_summary($this->filter_year, $this->filter_month),
            $this->get_outgoing_summary($this->filter_year, $this->filter_month)
        );
    }

    public function updatedFilterMonth(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_summary($this->filter_year, $this->filter_month),
            $this->get_outgoing_summary($this->filter_year, $this->filter_month)
        );
    }

    private function get_incoming_summary(string $filter_year, string $filter_month): array
    {
        $result = DashboardData::query()
            ->where('type', DashboardDataType::TYPE_AFFILIATE_DATA)
            ->get()
            ->groupBy(fn ($record) => Carbon::parse($record->data['from'])->format('Y-m'))
            ->sortBy(fn ($record) => Carbon::parse($record->first()->data['from']))
            ->map(fn ($month): array => ['amount' => round($month->sum(fn ($record) => $record->data['amount']), 2) + config('dashboard-data.additional_incomming_invoice_amount'), 'consultations' => $month->sum(fn ($record) => array_key_exists('qty', $record->data) ? $record->data['qty'] : 0)])
            ->toArray();

        return $this->filter_datas($result, $filter_year, $filter_month);
    }

    private function get_outgoing_summary(string $filter_year, string $filter_month): array
    {
        $result = [];
        $converter = new CurrencyCached(60 * 60 * 24);

        DirectInvoice::query()->get()->each(function ($record) use ($converter, &$result): void {
            $date = Carbon::parse($record->from)->format('Y-m');

            $total = get_invoice_net_total($record->data);
            $currency = strtoupper((string) $record->data['billing_data']['currency']);

            $converted_total = $converter->convert($total, 'EUR', $currency);

            if (! isset($result[$date])) {
                $result[$date] = round($converted_total, 2);
            } else {
                $result[$date] += round($converted_total, 2);
            }
        });

        return $this->filter_datas($result, $filter_year, $filter_month);
    }

    private function filter_datas(array $data, string $filter_year, string $filter_month): array
    {
        if ($data === []) {
            return $data;
        }

        if ($filter_year === '' && $filter_month === '') {
            return $data;
        }

        return array_filter($data, function ($date) use ($filter_month, $filter_year): bool {
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
        }, ARRAY_FILTER_USE_KEY);
    }

    private function merge_datas(array $incoming_datas, array $outgoing_datas): array
    {

        if ($incoming_datas === [] || $outgoing_datas === []) {
            return [];
        }

        $result = [];

        foreach ($incoming_datas as $date => $incoming_data) {
            $result[$date]['incoming_total'] = $incoming_data;

            if (! array_key_exists('outgoing_total', $result[$date])) {
                $result[$date]['outgoing_total'] = 0;
            }
        }

        foreach ($outgoing_datas as $date => $outgoing_data) {
            $result[$date]['outgoing_total'] = $outgoing_data;

            if (! array_key_exists('incoming_total', $result[$date])) {
                $result[$date]['incoming_total']['amount'] = 0;
                $result[$date]['incoming_total']['consultations'] = 0;
            }
        }

        ksort($result);

        foreach ($result as $date => $data) {
            $result[$date]['incoming_percentage'] = $data['incoming_total']['amount'] / max($data['outgoing_total'] + $data['incoming_total']['amount'], 1) * 100;
            $result[$date]['outgoing_percentage'] = $data['outgoing_total'] / max($data['outgoing_total'] + $data['incoming_total']['amount'], 1) * 100;
        }

        return $result;
    }
}
