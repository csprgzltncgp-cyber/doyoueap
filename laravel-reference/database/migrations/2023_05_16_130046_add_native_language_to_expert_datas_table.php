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
            $table->unsignedBigInteger('native_language')->after('min_inprogress_cases')->nullable();
            $table->foreign('native_language')->references('id')->on('language_skills')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_datas', function (Blueprint $table): void {});
    }
};
