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
        Schema::table('case_input_values', function (Blueprint $table): void {
            //
            //            $table->dropForeign(['riport_visible_for']);
            //            $table->dropColumn(['display_format','chart']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_input_values', function (Blueprint $table): void {
            //
        });
    }
};
