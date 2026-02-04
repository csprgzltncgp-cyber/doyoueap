<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class InactivateTelusCompaniesByCountry extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries_list = [
            3,  // Czech Republic
            4,  // Slovakia
            6,  // Romania
            13, // Moldova
            7,  // Serbia
            16, // North Macedonia
            14, // Albania
            20, // Ukraine
            15, // Kosovo
        ];

        Company::query()
            ->whereHas('org_datas', fn ($query) => $query->whereIn('country_id', $countries_list)->where('contract_holder_id', 1)) // Telus/Lifeworks
            ->get()->each(function (Company $company): void {
                if ($company->countries()->count() == 1) {

                    $company->active = false;
                    $company->updated_at = Carbon::now();
                    $company->save();

                    Log::info('Iterated company: '.$company->id);
                }
            });
    }
}
