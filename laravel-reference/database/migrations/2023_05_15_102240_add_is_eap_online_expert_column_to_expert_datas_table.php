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
        Schema::table('expert_datas', function (Blueprint $table): void {
            $table->boolean('is_eap_online_expert')->nullable()->default(false)->after('is_cgp_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_datas', function (Blueprint $table): void {
            //
        });
    }
};
