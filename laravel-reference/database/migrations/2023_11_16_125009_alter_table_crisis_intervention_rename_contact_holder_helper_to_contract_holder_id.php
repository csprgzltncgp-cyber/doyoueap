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
        Schema::table('crisis_interventions', function (Blueprint $table): void {
            $table->renameColumn('contact_holder_helper', 'contract_holder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_holder_id', function (Blueprint $table): void {
            //
        });
    }
};
