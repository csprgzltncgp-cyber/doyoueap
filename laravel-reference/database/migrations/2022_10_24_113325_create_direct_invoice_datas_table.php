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
        Schema::create('direct_invoice_datas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->boolean('is_name_shown')->default(false);

            $table->string('country');
            $table->string('postal_code');
            $table->string('city');
            $table->string('street');
            $table->string('house_number');
            $table->boolean('is_address_shown')->default(false);

            $table->string('po_number');
            $table->boolean('is_po_number_shown')->default(false);
            $table->boolean('is_po_number_changing')->default(false);
            $table->boolean('is_po_number_required')->default(false);

            $table->string('tax_number');
            $table->boolean('is_tax_number_shown')->default(false);

            $table->integer('payment_deadline');
            $table->boolean('is_payment_deadlie_shown')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_invoice_data');
    }
};
