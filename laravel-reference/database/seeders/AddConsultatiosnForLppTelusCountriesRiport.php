<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\RiportValue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AddConsultatiosnForLppTelusCountriesRiport extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // LPP SA
        $company = Company::query()->find(843);

        $psychological_permission = $company->permissions()->where('permission_id', 1)->first();

        if ($company && $psychological_permission) {
            $company->riports()->where('from', '>=', Carbon::now()->startOfYear()->startOfQuarter()->format('Y-m-d'))->each(function ($riport): void {
                // Delete previous 0 consultation records
                $riport->values->where('type', 500)->map(function ($riport_value): void {
                    if (in_array($riport_value->country_id, config('lifeworks-countries')) && (int) $riport_value->country_id !== 25) {
                        $riport_value->delete();
                    }
                });

                // Create new consultation number type values
                $riport->values->where('type', 7)->each(function ($riport_value): void {
                    if ((int) $riport_value->country_id === 25) {
                        return;
                    }

                    if (in_array($riport_value->country_id, config('lifeworks-countries'))) { // IF lifeworks/Telus country
                        if (in_array((int) $riport_value->value, [1, 11])) { // Is Psychological or Coaching
                            $this->create_riport_value($riport_value->riport_id, RiportValue::TYPE_CONSULTATION_NUMBER, 4, $riport_value->country_id, $riport_value->is_ongoing);
                        } else {
                            $this->create_riport_value($riport_value->riport_id, RiportValue::TYPE_CONSULTATION_NUMBER, 1, $riport_value->country_id, $riport_value->is_ongoing);
                        }
                    }
                });
            });
        }
    }

    public function create_riport_value($riport_id, $type, $value, $country_id, $is_ongoing): void
    {
        RiportValue::query()->create([
            'riport_id' => $riport_id,
            'type' => $type,
            'value' => $value,
            'country_id' => $country_id,
            'is_ongoing' => $is_ongoing,
        ]);
    }
}
