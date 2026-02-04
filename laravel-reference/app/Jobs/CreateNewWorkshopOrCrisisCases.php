<?php

namespace App\Jobs;

use App\Models\CrisisIntervention;
use App\Models\Workshop;
use App\Traits\ActivityIdPrefixTrait;
use App\Traits\ContractDateTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateNewWorkshopOrCrisisCases implements ShouldQueue
{
    use ActivityIdPrefixTrait;
    use ContractDateTrait;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('[JOB][CreateNewWorkshopOrCrisisCases] fired!');

        // check workshops
        $tomorrow = date('Y-m-d', strtotime('+ 1days'));
        $creationDate = date('Y-m-d', strtotime($tomorrow.'+ 1days'));

        $companies_with_workshop = DB::table('org_data')
            ->where('workshops_number', '>=', 1)
            ->where('contract_date', '!=', null)
            ->get();

        $companies_with_crisis = DB::table('org_data')
            ->where('crisis_number', '>=', 1)
            ->where('contract_date', '!=', null)
            ->get();

        foreach ($companies_with_workshop as $company) {
            $period_start_date = $this->getPeriodStart($company->contract_date);
            $period_end_date = $this->getPeriodEnd($company->contract_date);

            if ($period_end_date === $tomorrow) {
                $workshops = DB::table('workshops')
                    ->where(['company_id' => $company->company_id, 'country_id' => $company->country_id, 'free' => 1])
                    ->whereBetween('created_at', [$period_start_date, $period_end_date])->get();

                foreach ($workshops as $workshop) {
                    DB::table('workshops')
                        ->insert([
                            'country_id' => $workshop->country_id,
                            'company_id' => $workshop->company_id,
                            'free' => 1,
                            'gift' => 0,
                            'contracts_date' => $workshop->contracts_date,
                            'valuta' => $workshop->valuta,
                            'workshop_price' => $workshop->workshop_price,
                            'contract_holder_id' => $workshop->contract_holder_id,
                            'active' => 1,
                            'created_at' => $creationDate,
                        ]);

                    $last_insert_id = DB::getPdo()->lastInsertId();
                    $activity_id_pref = $this->getActivityIdPref($workshop->contract_holder_id);

                    DB::table('workshops')->where('id', $last_insert_id)
                        ->update([
                            'activity_id' => 'w'.$activity_id_pref.$last_insert_id,
                            'updated_at' => $tomorrow,
                        ]);

                    DB::table('org_data')->where(['company_id' => $workshop->company_id, 'country_id' => $workshop->country_id])
                        ->update([
                            'workshops_number' => DB::raw('workshops_number + 1'),
                        ]);

                    Log::info('[JOB][CreateNewWorkshopOrCrisisCases] new workshop: '.$last_insert_id);

                    if ($workshop->active) {
                        Workshop::query()->find($workshop->id)->delete();
                    }
                }
            }
        }

        foreach ($companies_with_crisis as $company) {
            $period_start_date = $this->getPeriodStart($company->contract_date);
            $period_end_date = $this->getPeriodEnd($company->contract_date);

            if ($period_end_date === $tomorrow) {
                $crisis_interventions = DB::table('crisis_interventions')
                    ->where(['company_id' => $company->company_id, 'country_id' => $company->country_id, 'free' => 1])
                    ->whereBetween('created_at', [$period_start_date, $period_end_date])->get();

                foreach ($crisis_interventions as $crisis) {
                    DB::table('crisis_interventions')
                        ->insert([
                            'country_id' => $crisis->country_id,
                            'company_id' => $crisis->company_id,
                            'free' => $crisis->free,
                            'contracts_date' => $crisis->contracts_date,
                            'valuta' => $crisis->valuta,
                            'crisis_price' => $crisis->crisis_price,
                            'contract_holder_id' => $crisis->contract_holder_id,
                            'active' => 1,
                            'created_at' => $creationDate,
                        ]);

                    $last_insert_id = DB::getPdo()->lastInsertId();
                    $activity_id_pref = $this->getActivityIdPref($crisis->contract_holder_id);

                    DB::table('crisis_interventions')->where('id', $last_insert_id)
                        ->update([
                            'activity_id' => 'ci'.$activity_id_pref.$last_insert_id,
                            'updated_at' => $tomorrow,
                        ]);

                    DB::table('org_data')->where(['company_id' => $crisis->company_id, 'country_id' => $crisis->country_id])
                        ->update([
                            'crisis_number' => DB::raw('crisis_number + 1'),
                        ]);

                    Log::info('[JOB][CreateNewWorkshopOrCrisisCases] new crisis: '.$last_insert_id);

                    if ($crisis->active) {
                        CrisisIntervention::query()->find($crisis->id)->delete();
                    }
                }
            }
        }
    }
}
