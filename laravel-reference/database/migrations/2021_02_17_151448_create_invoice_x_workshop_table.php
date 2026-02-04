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
        Schema::create('invoice_x_workshop', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('case_id');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('case_id')->references('id')->on('workshop_cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_x_workshop');
    }
};
