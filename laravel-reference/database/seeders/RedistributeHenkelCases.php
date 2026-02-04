<?php

namespace Database\Seeders;

use App\Models\Cases;
use App\Models\Company;
use App\Models\EapOnline\EapUser;
use Illuminate\Database\Seeder;

class RedistributeHenkelCases extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cases::query()->with('values')->where('company_id', 1318)->get()->each(function (Cases $case): void {
            $henkel_country = Company::query()->whereIn('id', [
                305, 1368, 1369, 1370, 1371, 1372, 1373, 1374, 1375, 1376, 1377, 1378, 1379, 1380, 1381, 1382, 1383, 1384, 1385, 1387, 1388, 1389, 1390,
            ])
                ->whereHas('org_datas', function ($query) use ($case): void {
                    $query->where('country_id', $case->country_id);
                })
                ->first();

            if ($henkel_country) {
                $case->update([
                    'company_id' => $henkel_country->id,
                ]);

                $case->values->where('case_input_id', 2)->first()->update(['value' => $henkel_country->id]);
            }
        });

        EapUser::query()->where('company_id', 1318)->get()->each(function (EapUser $user): void {
            $henkel_country = Company::query()->whereIn('id', [
                305, 1368, 1369, 1370, 1371, 1372, 1373, 1374, 1375, 1376, 1377, 1378, 1379, 1380, 1381, 1382, 1383, 1384, 1385, 1387, 1388, 1389, 1390,
            ])
                ->whereHas('org_datas', function ($query) use ($user): void {
                    $query->where('country_id', $user->country_id);
                })
                ->first();

            if ($henkel_country) {
                $user->update([
                    'company_id' => $henkel_country->id,
                ]);
            }
        });
    }
}
