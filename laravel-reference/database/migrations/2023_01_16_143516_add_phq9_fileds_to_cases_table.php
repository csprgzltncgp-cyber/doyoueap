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
        Schema::table('cases', function (Blueprint $table): void {
            $table->integer('phq9_opening')->nullable()->after('wos_survey_clicked');
            $table->integer('phq9_closing')->nullable()->after('phq9_opening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table): void {
            //
        });
    }
};
