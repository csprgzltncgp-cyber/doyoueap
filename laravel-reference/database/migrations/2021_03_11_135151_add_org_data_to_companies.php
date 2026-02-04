<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Magyar t-systems
        DB::table('org_data')->insert([
            'contact_holder' => 2,
            'company_id' => 494,
            'country_id' => 1,
            'org_id' => null,
            'head_count' => null,
            'created_at' => now(),
            'workshops_number' => 0,
            'crisis_number' => 0,
            'contract_date' => null,
        ]);

        // Dr Max
        DB::table('org_data')->insert([
            'contact_holder' => 2,
            'company_id' => 495,
            'country_id' => 2,
            'org_id' => null,
            'head_count' => null,
            'created_at' => now(),
            'workshops_number' => 0,
            'crisis_number' => 0,
            'contract_date' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
