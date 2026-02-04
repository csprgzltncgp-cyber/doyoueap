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
        Schema::create('crisis_interventions', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('activity_id')->nullable();
            $table->foreignId('company_id')->references('id')->on('companies');
            $table->foreignId('country_id')->references('id')->on('countries');
            $table->string('free')->nullable();
            $table->date('contracts_date')->nullable();
            $table->string('valuta')->nullable();
            $table->integer('crisis_price')->nullable();
            $table->integer('contact_holder_helper')->nullable();
            $table->integer('active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crisis_interventions');
    }
};
