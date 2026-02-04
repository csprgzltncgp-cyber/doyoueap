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
        Schema::table('invoices', function (Blueprint $table): void {
            $table->integer('workshop_total')->nullable()->after('payment_deadline');
            $table->integer('crisis_total')->nullable()->after('workshop_total');
            $table->integer('orientation_total')->nullable()->after('crisis_total');
            $table->integer('cases_total')->nullable()->after('orientation_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            //
        });
    }
};
