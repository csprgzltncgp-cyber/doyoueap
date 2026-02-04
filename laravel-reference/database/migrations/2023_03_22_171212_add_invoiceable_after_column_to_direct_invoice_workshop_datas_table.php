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
        Schema::table('direct_invoice_workshop_data', function (Blueprint $table): void {
            $table->timestamp('invoiceable_after')->nullable()->after('direct_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direct_invoice_workshop_datas', function (Blueprint $table): void {
            //
        });
    }
};
