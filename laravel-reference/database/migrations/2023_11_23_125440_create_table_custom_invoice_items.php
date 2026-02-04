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
        Schema::create('custom_invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('country_id')->references('id')->on('countries');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_invoice_items');
    }
};
