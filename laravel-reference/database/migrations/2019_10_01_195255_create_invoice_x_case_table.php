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
        Schema::create('invoice_x_case', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->comment('Megadja, hogy melyik számla');
            $table->unsignedBigInteger('case_id')->comment('Megadja, hogy melyik eset');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('case_id')->references('id')->on('cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_x_case');
    }
};
