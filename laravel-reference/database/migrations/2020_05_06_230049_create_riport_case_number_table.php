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
        Schema::create('riport_case_numbers', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->integer('number_of_cases');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('riport_id');
            $table->timestamps();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('riport_id')->references('id')->on('riports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riport_case_numbers');
    }
};
