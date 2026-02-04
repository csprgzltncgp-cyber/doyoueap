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
            $table->integer('max_inprogress_cases')->nullable()->after('completed_first');
            $table->integer('min_inprogress_cases')->nullable()->after('max_inprogress_cases');
            $table->boolean('can_accept_more_cases')->default(true)->after('min_inprogress_cases');
            $table->boolean('is_cgp_employee')->default(false)->after('can_accept_more_cases');
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
