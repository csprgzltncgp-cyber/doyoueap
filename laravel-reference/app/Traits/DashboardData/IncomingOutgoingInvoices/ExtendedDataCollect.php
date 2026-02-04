<?php

namespace App\Traits\DashboardData\IncomingOutgoingInvoices;

use App\Enums\ContractHolderEnum;
use App\Enums\DashboardDataType;
use App\Models\DashboardData;
use Carbon\Carbon;

trait ExtendedDataCollect
{
    private function get_incoming_datas(string $filter_year, string $filter_month, ContractHolderEnum $contract_holder): array
    {
        $result = [];

        DashboardData::query()->where('type', DashboardDataType::TYPE_AFFILIATE_DATA)->get()
            ->filter(fn ($record): bool => $record->data['contract_holder'] == $contract_holder->value)->each(function ($record) use (&$result): void {
                $date = Carbon::parse($record->data['from'])->format('Y-m');

                if (! isset($result['countries'][$record->data['country']]['companies'][$record->data['company']][$date])) {
                    $result['countries'][$record->data['country']]['companies'][$record->data['company']][$date]['amount'] = $record->data['amount'];
                    $result['countries'][$record->data['country']]['companies'][$record->data['company']][$date]['qty'] = array_key_exists('qty', $record->data) ? $record->data['qty'] : 0;
                } else {
                    $result['countries'][$record->data['country']]['companies'][$record->data['company']][$date]['amount'] += $record->data['amount'];
                    $result['countries'][$record->data['country']]['companies'][$record->data['company']][$date]['qty'] += array_key_exists('qty', $record->data) ? $record->data['qty'] : 0;
                }
            });

        $filteted = $this->filter_datas($result, $filter_year, $filter_month);

        return $this->summarize_incoming_datas($filteted);
    }

    private function filter_datas(array $data, string $filter_year, string $filter_month): array
    {
        if ($data === []) {
            return $data;
        }

        if ($filter_year === '' && $filter_month === '') {
            return $data;
        }

        $data['countries'] = array_map(function (array $country) use ($filter_year, $filter_month): array {
            $country['companies'] = array_map(fn ($company): ?array => array_filter($company, function ($date) use ($filter_year, $filter_month): bool {
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

            }, ARRAY_FILTER_USE_KEY), $country['companies']);

            $country['companies'] = array_filter($country['companies'], fn ($company): bool => $company !== null && $company !== []);

            return $country;
        }, $data['countries']);

        $data['countries'] = array_filter($data['countries'], fn ($country): bool => $country !== null && $country !== []);

        return $data;
    }

    private function summarize_incoming_datas(array $data): array
    {
        if ($data === []) {
            return $data;
        }

        foreach ($data['countries'] as $country_id => $country) {
            $data['countries'][$country_id]['total_amount'] = 0;
            $data['countries'][$country_id]['total_qty'] = 0;

            foreach ($country['companies'] as $company_id => $company) {
                $data['countries'][$country_id]['companies'][$company_id]['total_amount'] = 0;
                $data['countries'][$country_id]['companies'][$company_id]['total_qty'] = 0;

                foreach ($company as $values) {
                    $data['countries'][$country_id]['companies'][$company_id]['total_amount'] += $values['amount'];
                    $data['countries'][$country_id]['total_amount'] += $values['amount'];

                    $data['countries'][$country_id]['companies'][$company_id]['total_qty'] += $values['qty'];
                    $data['countries'][$country_id]['total_qty'] += $values['qty'];
                }
            }
        }

        $data['total_amount'] = 0;
        $data['total_qty'] = 0;

        foreach ($data['countries'] as $country) {
            $data['total_amount'] += $country['total_amount'];
            $data['total_qty'] += $country['total_qty'];
        }

        return $data;
    }

    private function summarize_outgoing_datas(array $data): array
    {
        if ($data === []) {
            return $data;
        }

        foreach ($data['countries'] as $country_id => $country) {
            $data['countries'][$country_id]['total'] = 0;

            foreach ($country['companies'] as $company_id => $company) {
                $data['countries'][$country_id]['companies'][$company_id]['total'] = 0;

                foreach ($company as $amount) {
                    $data['countries'][$country_id]['companies'][$company_id]['total'] += $amount;
                    $data['countries'][$country_id]['total'] += $amount;
                }
            }
        }

        $data['total'] = 0;

        foreach ($data['countries'] as $country) {
            $data['total'] += $country['total'];
        }

        return $data;
    }

    private function merge_datas(array $incoming_datas, array $outgoing_datas): array
    {
        if ($incoming_datas === [] || $outgoing_datas === []) {
            return [];
        }

        $result = [
            'incoming_total_amount' => $incoming_datas['total_amount'],
            'incoming_total_qty' => $incoming_datas['total_qty'],
            'outgoing_total' => $outgoing_datas['total'],
            'incoming_percentage' => $outgoing_datas['total'] / max($outgoing_datas['total'] + $incoming_datas['total_amount'], 1) * 100,
            'outgoing_percentage' => $incoming_datas['total_amount'] / max($outgoing_datas['total'] + $incoming_datas['total_amount'], 1) * 100,
            'countries' => [],
        ];

        foreach ($incoming_datas['countries'] as $country_id => $country_data) {
            if (array_key_exists($country_id, $result['countries'])) {
                $result['countries'][$country_id]['incoming_total_amount'] = $country_data['total'];
            } else {
                $result['countries'][$country_id] = [
                    'incoming_total_amount' => $country_data['total_amount'],
                    'incoming_total_qty' => $country_data['total_qty'],
                    'outgoing_total' => 0,
                    'companies' => [],
                ];
            }

            foreach ($country_data['companies'] as $company_id => $company_data) {
                if (array_key_exists($company_id, $result['countries'][$country_id]['companies'])) {
                    $result['countries'][$country_id]['companies'][$company_id]['incoming_total_amount'] = $company_data['total'];
                } else {
                    $result['countries'][$country_id]['companies'][$company_id] = [
                        'incoming_total_amount' => $company_data['total_amount'],
                        'incoming_total_qty' => $company_data['total_qty'],
                        'outgoing_total' => 0,
                    ];
                }
            }
        }

        foreach ($outgoing_datas['countries'] as $country_id => $company_data) {
            if (array_key_exists($country_id, $result['countries'])) {
                $result['countries'][$country_id]['outgoing_total'] = $company_data['total'];
            } else {
                $result['countries'][$country_id] = [
                    'incoming_total_amount' => 0,
                    'incoming_total_qty' => 0,
                    'outgoing_total' => $company_data['total'],
                    'companies' => [],
                ];
            }

            foreach ($company_data['companies'] as $company_id => $company_data) {
                if (array_key_exists($company_id, $result['countries'][$country_id]['companies'])) {
                    $result['countries'][$country_id]['companies'][$company_id]['outgoing_total'] = $company_data['total'];
                } else {
                    $result['countries'][$country_id]['companies'][$company_id] = [
                        'incoming_total_amount' => 0,
                        'incoming_total_qty' => 0,
                        'outgoing_total' => $company_data['total'],
                    ];
                }
            }
        }

        foreach ($result['countries'] as $country_id => $country_data) {
            $result['countries'][$country_id]['incoming_percentage'] = $country_data['outgoing_total'] / max($country_data['outgoing_total'] + $country_data['incoming_total_amount'], 1) * 100;
            $result['countries'][$country_id]['outgoing_percentage'] = $country_data['incoming_total_amount'] / max($country_data['outgoing_total'] + $country_data['incoming_total_amount'], 1) * 100;

            foreach ($country_data['companies'] as $company_id => $company_data) {
                $result['countries'][$country_id]['companies'][$company_id]['incoming_percentage'] = $company_data['outgoing_total'] / max($company_data['outgoing_total'] + $company_data['incoming_total_amount'], 1) * 100;
                $result['countries'][$country_id]['companies'][$company_id]['outgoing_percentage'] = $company_data['incoming_total_amount'] / max($company_data['outgoing_total'] + $company_data['incoming_total_amount'], 1) * 100;
            }
        }

        foreach ($result['countries'] as $country_id => $country_data) {
            $result['countries'][$country_id]['problem'] = false;

            foreach (array_keys($country_data['companies']) as $company_id) {
                if ($result['countries'][$country_id]['companies'][$company_id]['incoming_percentage'] < $result['countries'][$country_id]['companies'][$company_id]['outgoing_percentage']) {
                    $result['countries'][$country_id]['problem'] = true;
                }
            }
        }

        return $result;
    }
}
