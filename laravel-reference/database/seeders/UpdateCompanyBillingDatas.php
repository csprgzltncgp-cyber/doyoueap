<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\DirectInvoiceData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UpdateCompanyBillingDatas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eu_country_ids = [
            1, 2, 3, 4, 6, 8, 9, 10, 11, 12,
            23, 31, 32, 38, 43, 44, 46, 47, 48, 52,
            53, 54, 55, 57, 58, 60, 61,
        ];

        DirectInvoiceData::query()->each(function (DirectInvoiceData $data) use ($eu_country_ids): void {
            if (data_get($data, 'direct_billing_data') && $data->direct_billing_data->tehk) {
                if (! $data->community_tax_number || Str::length($data->community_tax_number) <= 3) {
                    return;
                }

                if (in_array($data->country_id, $eu_country_ids)) {
                    $data->direct_billing_data->update([
                        'inside_eu' => 1,
                        'outside_eu' => 0,
                    ]);
                } else { // Try to find the country based on the country name field in the invoice data.
                    $country = Country::query()->where('name', $data->country)->first();
                    if ($country && in_array($country->id, $eu_country_ids)) {
                        $data->direct_billing_data->update([
                            'inside_eu' => 1,
                            'outside_eu' => 0,
                        ]);
                    } else {
                        $data->direct_billing_data->update([
                            'outside_eu' => 1,
                            'inside_eu' => 0,
                        ]);
                    }
                }
            }
        });
    }
}
