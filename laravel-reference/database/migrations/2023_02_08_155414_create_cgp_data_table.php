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
        Schema::create('cgp_data', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name');
            $table->string('country');
            $table->string('post_code');
            $table->string('city');
            $table->string('street');
            $table->string('house_number');
            $table->string('vat_number');
            $table->string('eu_vat_number');
            $table->string('iban');
            $table->string('swift');
            $table->string('email');
            $table->string('website');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cgp_data');
    }
};
