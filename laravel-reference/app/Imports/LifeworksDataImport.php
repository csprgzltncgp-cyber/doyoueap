<?php

namespace App\Imports;

use App\Enums\ContractHolderEnum;
use App\Enums\DashboardDataType;
use App\Helpers\CurrencyCached;
use App\Models\Country;
use App\Models\DashboardData;
use App\Models\OrgData;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LifeworksDataImport implements ToArray, WithHeadingRow
{
    public function __construct(
        public Carbon $date,
    ) {}

    public function array(array $rows): void
    {
        $converter = new CurrencyCached(60 * 60 * 24);

        foreach ($rows as $row) {
            if (! array_key_exists('org_ids', $row)) {
                continue;
            }

            if (! array_key_exists('country', $row)) {
                continue;
            }

            $country = Country::query()->where('name', 'like', '%'.strtolower((string) $row['country']).'%')->first();

            if (! $country) {
                continue;
            }

            if ((int) $row['org_ids'] === 0) {
                continue;
            }

            $org_data = OrgData::query()->where('org_id', (int) $row['org_ids'])->where('country_id', $country->id)->first();
            if (! $org_data) {
                continue;
            }
            if (! $org_data->company) {
                continue;
            }

            if (! array_key_exists('rate', $row)) {
                continue;
            }

            $month = strtolower($this->date->format('F'));

            if (! array_key_exists($month, $row)) {
                continue;
            }

            $rate = $converter->convert((float) $row['rate'], 'EUR', 'USD');

            DashboardData::query()->create([
                'type' => DashboardDataType::TYPE_LIFEWORKS_DATA,
                'data' => [
                    'company' => $org_data->company->id,
                    'country' => $org_data->country->id,
                    'contract_holder' => ContractHolderEnum::LIFEWORKS->value,
                    'amount' => round($rate * (int) $row[$month], 2),
                    'from' => $this->date->startOfMonth()->format('Y-m-d'),
                    'to' => $this->date->endOfMonth()->format('Y-m-d'),
                ],
            ]);
        }
    }
}
