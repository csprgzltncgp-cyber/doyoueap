<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\EapOnline\EapRiport;
use App\Models\EapOnline\EapRiportValue;
use App\Models\EapOnline\Statistics\EapCategory;
use Illuminate\Database\Seeder;

class RedistributeHenkelRiportsEapCategories extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $henkel_country = Company::query()->whereIn('id', [
            305, 1368, 1369, 1370, 1371, 1372, 1373, 1374, 1375, 1376, 1377,
            1378, 1379, 1380, 1381, 1382, 1383, 1384, 1385, 1387, 1388, 1389,
            1390,
        ])->get();

        // Merge login statistic per country
        $henkel_country->each(function (Company $company): void {
            EapRiport::query()->where('company_id', $company->id)->get()->each(function (EapRiport $eap_riport): void {

                $statistics_types = $eap_riport->eap_riport_values()
                    ->where(['statistics' => EapCategory::class])
                    ->select('statistics_type')
                    ->distinct()
                    ->pluck('statistics_type');

                $statistics_subtypes = $eap_riport->eap_riport_values()
                    ->where(['statistics' => EapCategory::class])
                    ->select('statistics_subtype')
                    ->distinct()
                    ->pluck('statistics_subtype');

                $statistics_types->each(function (int $statistic_type) use ($statistics_subtypes, $eap_riport): void {
                    $statistics_subtypes->each(function (int $statistics_subtype) use ($eap_riport, $statistic_type): void {
                        $category_values = optional($eap_riport->eap_riport_values()
                            ->where(['statistics' => EapCategory::class])
                            ->where('statistics_type', $statistic_type)
                            ->where('statistics_subtype', $statistics_subtype)
                            ->get());

                        if ($category_values->count() >= 2) {
                            // Create new EapRiportValue with the summed value
                            EapRiportValue::query()->create([
                                'riport_id' => $eap_riport->id,
                                'country_id' => $category_values->first()->country_id,
                                'statistics' => $category_values->first()->statistics,
                                'statistics_type' => $statistic_type,
                                'statistics_subtype' => $statistics_subtype,
                                'count' => $category_values->sum('count'),
                            ]);

                            // Remove previous values
                            $category_values->map->delete();
                        }
                    });
                });
            });
        });
    }
}
