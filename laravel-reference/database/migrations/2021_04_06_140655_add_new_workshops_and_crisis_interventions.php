<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = [[112, 1], [25, 1], [28, 1], [335, 2], [274, 1], [196, 3]];

        foreach ($companies as $data) {
            $workshops = DB::table('workshops')
                ->where(['company_id' => $data[0], 'country_id' => $data[1]])->get();

            $interventions = DB::table('crisis_interventions')
                ->where(['company_id' => $data[0], 'country_id' => $data[1]])->get();

            foreach ($workshops as $workshop) {
                DB::table('workshops')
                    ->insert([
                        'country_id' => $workshop->country_id,
                        'company_id' => $workshop->company_id,
                        'free' => $workshop->free,
                        'contracts_date' => $workshop->contracts_date,
                        'valuta' => $workshop->valuta,
                        'workshop_price' => $workshop->workshop_price,
                        'contact_holder_helper' => $workshop->contact_holder_helper,
                        'active' => 1,
                        'created_at' => now(),
                    ]);

                $last_insert_id = DB::getPdo()->lastInsertId();
                $activity_id_pref = 'cgp';

                DB::table('workshops')->where('id', $last_insert_id)
                    ->update([
                        'activity_id' => 'w'.$activity_id_pref.$last_insert_id,
                        'updated_at' => now(),
                    ]);

                DB::table('org_data')->where(['company_id' => $workshop->company_id, 'country_id' => $workshop->country_id])
                    ->update([
                        'workshops_number' => DB::raw('workshops_number + 1'),
                    ]);
            }

            foreach ($interventions as $crisis) {
                DB::table('crisis_interventions')
                    ->insert([
                        'country_id' => $crisis->country_id,
                        'company_id' => $crisis->company_id,
                        'free' => $crisis->free,
                        'contracts_date' => $crisis->contracts_date,
                        'valuta' => $crisis->valuta,
                        'crisis_price' => $crisis->crisis_price,
                        'contact_holder_helper' => $crisis->contact_holder_helper,
                        'active' => 1,
                        'created_at' => now(),
                    ]);

                $last_insert_id = DB::getPdo()->lastInsertId();
                $activity_id_pref = 'cgp';

                DB::table('crisis_interventions')->where('id', $last_insert_id)
                    ->update([
                        'activity_id' => 'ci'.$activity_id_pref.$last_insert_id,
                        'updated_at' => now(),
                    ]);

                DB::table('org_data')->where(['company_id' => $crisis->company_id, 'country_id' => $crisis->country_id])
                    ->update([
                        'crisis_number' => DB::raw('crisis_number + 1'),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
