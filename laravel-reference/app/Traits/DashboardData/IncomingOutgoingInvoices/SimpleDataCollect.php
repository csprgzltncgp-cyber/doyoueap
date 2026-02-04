<?php

namespace App\Traits\DashboardData\IncomingOutgoingInvoices;

use App\Enums\ContractHolderCompany;
use App\Enums\ContractHolderEnum;
use App\Enums\DashboardDataType;
use App\Helpers\CurrencyCached;
use App\Models\DashboardData;
use App\Models\DirectInvoice;
use Carbon\Carbon;

trait SimpleDataCollect
{
    private function get_incoming_datas(string $filter_year, string $filter_month, ContractHolderEnum $contract_holder): array
    {
        $result = [];

        DashboardData::query()->where('type', DashboardDataType::TYPE_AFFILIATE_DATA)->get()
            ->filter(fn ($record): bool => $record->data['contract_holder'] == $contract_holder->value)->each(function ($record) use (&$result): void {
                $date = Carbon::parse($record->data['from'])->format('Y-m');

                if (! isset($result[$date])) {
                    $result[$date]['amount'] = $record->data['amount'];
                    $result[$date]['qty'] = array_key_exists('qty', $record->data) ? $record->data['qty'] : 0;
                } else {
                    $result[$date]['amount'] += $record->data['amount'];
                    $result[$date]['qty'] += array_key_exists('qty', $record->data) ? $record->data['qty'] : 0;
                }
            })->sortByDesc('from');

        return $this->filter_datas($result, $filter_year, $filter_month);
    }

    private function get_outgoing_datas(string $filter_year, string $filter_month, ContractHolderCompany $contract_holder): array
    {
        $result = [];

        $converter = new CurrencyCached(60 * 60 * 24);

        $data = DirectInvoice::query()->with(['country', 'company', 'company.countries'])->whereHas('company', function ($query) use ($contract_holder): void {
            $query->where('id', $contract_holder->value);
        })->get();

        $data->each(function ($record) use (&$result, $converter): void {
            $date = Carbon::parse($record->from)->format('Y-m');

            $total = get_invoice_net_total($record->data);
            $currency = strtoupper((string) $record->data['billing_data']['currency']);

            $converted_total = $converter->convert($total, 'EUR', $currency);

            if (! isset($result[$date])) {
                $result[$date] = $converted_total;
            } else {
                $result[$date] += $converted_total;
            }
        })->sortByDesc('from');

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

    public function merge_datas(array $incoming_datas, array $outgoing_datas): array
    {
        $result = [];
        foreach ($incoming_datas as $date => $values) {
            if (! isset($result[$date])) {
                $result[$date] = [
                    'incoming' => [
                        'amount' => $values['amount'],
                        'qty' => $values['qty'],
                    ],
                    'outgoing' => 0,
                ];
            } else {
                $result[$date]['incoming'] = [
                    'amount' => $values['amount'],
                    'qty' => $values['qty'],
                ];
            }
        }

        foreach ($outgoing_datas as $date => $amount) {
            if (! isset($result[$date])) {
                $result[$date] = [
                    'incoming' => 0,
                    'outgoing' => $amount,
                ];
            } else {
                $result[$date]['outgoing'] = $amount;
            }
        }

        ksort($result);

        return $result;
    }
}
