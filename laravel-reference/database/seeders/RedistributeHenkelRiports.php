<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Riport;
use App\Models\RiportValue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RedistributeHenkelRiports extends Seeder
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
        ])
            ->get();

        $henkel_country->each(function (Company $company): void {
            $henkel_ag_riports = Riport::query()
                ->with('values')
                ->where('company_id', 1318) // Henkel AG & Co. KGaA,
                ->whereHas('values', fn ($query) => $query->where('country_id', $company->org_datas->first()->country_id))
                ->get();

            if ($henkel_ag_riports->isEmpty()) {
                return;
            }

            $henkel_ag_riports->each(function (Riport $henkel_ag_riport) use ($company): void {
                $riport_month = Riport::query()
                    ->with('values')
                    ->where('company_id', $company->id)
                    ->where('from', $henkel_ag_riport->from)
                    ->where('to', $henkel_ag_riport->to)
                    ->first();

                // Create month riport for company
                if (! $riport_month) {
                    if (Carbon::parse($henkel_ag_riport->from)->isCurrentYear()) {
                        $active = ! Carbon::parse($henkel_ag_riport->from)->isCurrentQuarter();
                    } else {
                        $active = true;
                    }

                    $new_riport = Riport::query()->create([
                        'company_id' => $company->id,
                        'from' => $henkel_ag_riport->from,
                        'to' => $henkel_ag_riport->to,
                        'is_active' => $active,
                    ]);

                    $riport_month = $new_riport;
                }

                // Set Henkel AG & Co. KGaA riport values riport id to the correct Henke Company riport id
                $henkel_ag_riport->values->where('country_id', $company->org_datas->first()->country_id)->each(function (RiportValue $value) use ($riport_month): void {
                    $value->update([
                        'riport_id' => $riport_month->id,
                    ]);
                });
            });
        });
    }
}
