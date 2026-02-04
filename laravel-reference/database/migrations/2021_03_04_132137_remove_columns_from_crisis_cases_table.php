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
            $table->dropColumn('select_topic');
            $table->dropColumn('number_of_participants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crisis_cases', function (Blueprint $table): void {
            $table->longText('select_topic');
            $table->bigInteger('number_of_participants');
        });
    }
};
