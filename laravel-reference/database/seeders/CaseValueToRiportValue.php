<?php

namespace Database\Seeders;

use App\Models\Cases;
use App\Models\Riport;
use App\Models\RiportValue;
use Illuminate\Database\Seeder;

class CaseValueToRiportValue extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Riport::query()->where('company_id', 1250)->where('from', '>=', '2025-04-01')->get()->each(function ($riport): void {
            $cases = Cases::query()
                ->where('company_id', 1250)
                ->whereBetween('created_at', [$riport->from->startOfMonth(), $riport->from->endOfMonth()])
                ->get();

            $cases->each(function ($cases) use ($riport): void {
                $country_id = $cases->country_id;
                $value = $cases->values->where('case_input_id', 88)->where('value', '!=', '-')->first();

                if ($value) {
                    RiportValue::query()->create([
                        'riport_id' => $riport->id,
                        'country_id' => $country_id,
                        'value' => $value->value,
                        'type' => 88,
                    ]);
                }
            });
        });
    }
}
