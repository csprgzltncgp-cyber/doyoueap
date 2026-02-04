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
        Schema::table('direct_invoice_datas', function (Blueprint $table): void {
            $table->boolean('invoicing_inactive')->default(false)->after('is_payment_deadlie_shown');
            $table->timestamp('invoicing_inactive_from')->nullable()->after('invoicing_inactive');
            $table->timestamp('invoicing_inactive_to')->nullable()->after('invoicing_inactive_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
