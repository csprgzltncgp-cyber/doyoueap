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
        Schema::create('direct_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->unsignedBigInteger('direct_invoice_data_id')->nullable();
            $table->json('data');
            $table->date('from');
            $table->date('to');
            $table->string('invoice_number')->nullable();

            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('invoice_uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_invoices');
    }
};
