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
        Schema::table('country_differentiates', function (Blueprint $table): void {
            $table->boolean('contract_date_reminder_email')->after('invoicing')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_differentiates', function (Blueprint $table): void {
            //
        });
    }
};
