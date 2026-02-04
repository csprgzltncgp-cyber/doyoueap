<?php

namespace Database\Seeders;

use App\Models\CustomerSatisfaction;
use App\Models\CustomerSatisfactionValue;
use App\Models\Riport;
use App\Models\RiportValue;
use Illuminate\Database\Seeder;

class MigrateHenkelCountryData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* CustomerSatisfaction::query()
            ->where('company_id', 24)
            ->whereHas('values', fn($query) => $query->where('country_id', 4))
            ->get()->each(function(CustomerSatisfaction $satisfaction) {
                $new_satisfaction = CustomerSatisfaction::query()->create([
                    'from' => $satisfaction->from,
                    'to' => $satisfaction->to,
                    'company_id' => 1508,
                    'is_active' => $satisfaction->is_active,
                ]);

                $satisfaction->values->where('country_id', 4)->each(function(CustomerSatisfactionValue $value) use ($new_satisfaction) {
                    $value->update([
                        'customer_satisfaction_id' => $new_satisfaction->id
                    ]);
                });
            }); */

        Riport::query()
            ->where('company_id', 24)
            ->where('from', '>=', '2022-01-01')
            ->where('to', '<=', '2022-12-31')
            ->whereHas('values', fn ($query) => $query->where('country_id', 4))
            ->get()->each(function (Riport $riport): void {
                $new_riport = Riport::query()->create([
                    'from' => $riport->from,
                    'to' => $riport->to,
                    'company_id' => 1508,
                    'is_active' => $riport->is_active,
                ]);

                $riport->values->where('country_id', 4)->each(function (RiportValue $value) use ($new_riport): void {
                    $value->update([
                        'riport_id' => $new_riport->id,
                    ]);
                });
            });
    }
}
