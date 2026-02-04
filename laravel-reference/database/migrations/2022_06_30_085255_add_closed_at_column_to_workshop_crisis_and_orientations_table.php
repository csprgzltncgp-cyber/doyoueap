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
        Schema::table('workshop_cases', function (Blueprint $table): void {
            $table->timestamp('closed_at')->nullable()->after('number_of_participants');
        });

        Schema::table('crisis_cases', function (Blueprint $table): void {
            $table->timestamp('closed_at')->nullable()->after('expert_valuta');
        });

        Schema::table('orientations', function (Blueprint $table): void {
            $table->timestamp('closed_at')->nullable()->after('paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_cases', function (Blueprint $table): void {
            //
        });
    }
};
