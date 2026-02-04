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
        Schema::table('direct_billing_datas', function (Blueprint $table): void {
            $table->foreignId('direct_invoice_data_id')->after('id')->nullable()->constrained('direct_invoice_datas')->onDelete('cascade');
        });

        Schema::table('invoice_items', function (Blueprint $table): void {
            $table->foreignId('direct_invoice_data_id')->after('id')->nullable()->constrained('direct_invoice_datas')->onDelete('cascade');
        });

        Schema::table('invoice_comments', function (Blueprint $table): void {
            $table->foreignId('direct_invoice_data_id')->after('id')->nullable()->constrained('direct_invoice_datas')->onDelete('cascade');
        });

        Schema::table('invoice_notes', function (Blueprint $table): void {
            $table->foreignId('direct_invoice_data_id')->after('id')->nullable()->constrained('direct_invoice_datas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table): void {
            //
        });
    }
};
