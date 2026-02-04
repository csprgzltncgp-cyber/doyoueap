<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crisis_cases', function (Blueprint $table): void {
            $table->renameColumn('select_campany', 'company_id');
            $table->renameColumn('select_company_contact_name', 'company_contact_name');
            $table->renameColumn('select_company_contact_email', 'company_contact_email');
            $table->renameColumn('select_company_contact_phone', 'company_contact_phone');
            $table->renameColumn('select_country', 'country_id');
            $table->renameColumn('select_city', 'city_id');
            $table->renameColumn('select_expert', 'expert_id');
            $table->renameColumn('select_expert_phone', 'expert_phone');
            $table->renameColumn('select_date', 'date');
            $table->renameColumn('select_start_time', 'start_time');
            $table->renameColumn('select_end_time', 'end_time');
            $table->renameColumn('select_full_time', 'full_time');
            $table->renameColumn('select_activity_id', 'activity_id');
            $table->renameColumn('select_language', 'language_id');
            $table->renameColumn('select_price', 'price');
            $table->renameColumn('select_valuta', 'currency');
            $table->renameColumn('expert_valuta', 'expert_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
