<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('invoice_datas', function (Blueprint $table): void {
            DB::statement("ALTER TABLE invoice_events CHANGE COLUMN event event ENUM('invoice_expired_and_not_paid', 'invoice_paid', 'invoice_payment_sent') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_datas', function (Blueprint $table): void {
            //
        });
    }
};
