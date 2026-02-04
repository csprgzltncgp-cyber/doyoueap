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
        Schema::table('riport_values_count', function (Blueprint $table): void {
            //
            $table->unsignedBigInteger('case_input_values_id')->nullable()->change();
            $table->unsignedBigInteger('city_id')->nullable()->after('case_input_values_id');
            $table->unsignedBigInteger('permission_id')->nullable()->after('city_id');

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riport_values_count', function (Blueprint $table): void {
            //
        });
    }
};
