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
            //
            $table->unsignedBigInteger('closed_by_expert')->nullable()->after('customer_satisfaction_not_possible');

            $table->foreign('closed_by_expert')->references('id')->on('users');
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
