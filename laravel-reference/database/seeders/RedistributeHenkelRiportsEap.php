<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\EapOnline\EapRiport;
use App\Models\EapOnline\EapRiportValue;
use App\Models\EapOnline\Statistics\EapAssessment;
use App\Models\EapOnline\Statistics\EapLogin;
use App\Models\EapOnline\Statistics\EapSelfHelp;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RedistributeHenkelRiportsEap extends Seeder
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
            $henkel_ag_riports = EapRiport::query()
                ->with('eap_riport_values')
                ->where('company_id', 1318) // Henkel AG & Co. KGaA,
                ->whereHas('eap_riport_values', fn ($query) => $query->where('country_id', $company->org_datas->first()->country_id))
                ->get();

            if ($henkel_ag_riports->isEmpty()) {
                return;
            }

            $henkel_ag_riports->each(function (EapRiport $henkel_ag_riport) use ($company): void {
                $riport_month = EapRiport::query()
                    ->with('eap_riport_values')
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

                    $new_riport = EapRiport::query()->create([
                        'company_id' => $company->id,
                        'from' => $henkel_ag_riport->from,
                        'to' => $henkel_ag_riport->to,
                        'is_active' => $active,
                    ]);

                    $riport_month = $new_riport;
                }

                // Set Henkel AG & Co. KGaA riport eap_riport_values riport id to the correct Henke Company riport id
                $henkel_ag_riport->eap_riport_values->where('country_id', $company->org_datas->first()->country_id)->each(function (EapRiportValue $value) use ($riport_month): void {
                    $value->update([
                        'riport_id' => $riport_month->id,
                    ]);
                });
            });
        });

        // Merge login statistic per country
        $henkel_country->each(function (Company $company): void {
            EapRiport::query()->where('company_id', $company->id)->get()->each(function (EapRiport $eap_riport): void {
                $login_values = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapLogin::class])
                    ->get());

                if ($login_values->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $login_values->first()->country_id,
                        'statistics' => $login_values->first()->statistics,
                        'statistics_type' => null,
                        'statistics_subtype' => null,
                        'count' => $login_values->sum('count'),
                    ]);

                    // Remove previous values
                    $login_values->map->delete();
                }

                $assessment_values_1 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapAssessment::class])
                    ->where('statistics_subtype', 1)
                    ->get());

                if ($assessment_values_1->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $assessment_values_1->first()->country_id,
                        'statistics' => $assessment_values_1->first()->statistics,
                        'statistics_type' => null,
                        'statistics_subtype' => 1,
                        'count' => $assessment_values_1->sum('count'),
                    ]);

                    // Remove previous values
                    $assessment_values_1->map->delete();
                }

                $assessment_values_2 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapAssessment::class])
                    ->where('statistics_subtype', 2)
                    ->get());

                if ($assessment_values_2->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $assessment_values_2->first()->country_id,
                        'statistics' => $assessment_values_2->first()->statistics,
                        'statistics_type' => null,
                        'statistics_subtype' => 2,
                        'count' => $assessment_values_2->sum('count'),
                    ]);

                    // Remove previous values
                    $assessment_values_2->map->delete();
                }

                $assessment_values_3 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapAssessment::class])
                    ->where('statistics_subtype', 3)
                    ->get());

                if ($assessment_values_3->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $assessment_values_3->first()->country_id,
                        'statistics' => $assessment_values_3->first()->statistics,
                        'statistics_type' => null,
                        'statistics_subtype' => 3,
                        'count' => $assessment_values_3->sum('count'),
                    ]);

                    // Remove previous values
                    $assessment_values_3->map->delete();
                }

                $self_help_values_1 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapSelfHelp::class])
                    ->where('statistics_type', 1)
                    ->get());

                if ($self_help_values_1->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $self_help_values_1->first()->country_id,
                        'statistics' => $self_help_values_1->first()->statistics,
                        'statistics_type' => 1,
                        'statistics_subtype' => null,
                        'count' => $self_help_values_1->sum('count'),
                    ]);

                    // Remove previous values
                    $self_help_values_1->map->delete();
                }

                $self_help_values_5 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapSelfHelp::class])
                    ->where('statistics_type', 5)
                    ->get());

                if ($self_help_values_5->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $self_help_values_5->first()->country_id,
                        'statistics' => $self_help_values_5->first()->statistics,
                        'statistics_type' => 5,
                        'statistics_subtype' => null,
                        'count' => $self_help_values_5->sum('count'),
                    ]);

                    // Remove previous values
                    $self_help_values_5->map->delete();
                }

                $self_help_values_6 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapSelfHelp::class])
                    ->where('statistics_type', 6)
                    ->get());

                if ($self_help_values_6->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $self_help_values_6->first()->country_id,
                        'statistics' => $self_help_values_6->first()->statistics,
                        'statistics_type' => 6,
                        'statistics_subtype' => null,
                        'count' => $self_help_values_6->sum('count'),
                    ]);

                    // Remove previous values
                    $self_help_values_6->map->delete();
                }

                $self_help_values_7 = optional($eap_riport->eap_riport_values()
                    ->where(['statistics' => EapSelfHelp::class])
                    ->where('statistics_type', 7)
                    ->get());

                if ($self_help_values_7->count() >= 2) {
                    // Create new EapRiportValue with the summed value
                    EapRiportValue::query()->create([
                        'riport_id' => $eap_riport->id,
                        'country_id' => $self_help_values_7->first()->country_id,
                        'statistics' => $self_help_values_7->first()->statistics,
                        'statistics_type' => 7,
                        'statistics_subtype' => null,
                        'count' => $self_help_values_7->sum('count'),
                    ]);

                    // Remove previous values
                    $self_help_values_7->map->delete();
                }
            });
        });
    }
}
