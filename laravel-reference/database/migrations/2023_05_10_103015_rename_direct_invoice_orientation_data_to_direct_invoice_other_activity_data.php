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
        Schema::table('direct_invoice_orientation_data', function (Blueprint $table): void {
            $table->renameColumn('orientation_id', 'other_activity_id');
            $table->dropIndex('direct_invoice_orientation_data_direct_invoice_id_foreign');
            $table->dropIndex('direct_invoice_orientation_data_country_id_foreign');
            $table->dropIndex('direct_invoice_orientation_data_company_id_foreign');
        });

        Schema::rename('direct_invoice_orientation_data', 'direct_invoice_other_activity_data');

        Schema::table('direct_invoice_other_activity_data', function (Blueprint $table): void {
            $table->index(['direct_invoice_id']);
            $table->index(['country_id']);
            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direct_invoice_other_activity_data', function (Blueprint $table): void {
            //
        });
    }
};
