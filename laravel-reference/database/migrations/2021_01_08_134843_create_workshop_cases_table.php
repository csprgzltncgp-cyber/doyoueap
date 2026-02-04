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
        Schema::dropIfExists('workshop_cases');

        Schema::create('workshop_cases', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('select_campany')->references('id')->on('companies');
            $table->string('select_company_contact_name')->nullable();
            $table->string('select_company_contact_email')->nullable();
            $table->string('select_company_contact_phone')->nullable();
            $table->foreignId('select_country')->references('id')->on('countries');
            $table->foreignId('select_city')->references('id')->on('cities');
            $table->foreignId('select_expert')->references('id')->on('users');
            $table->string('select_expert_phone')->nullable();
            $table->date('select_date')->nullable();
            $table->string('select_start_time')->nullable();
            $table->string('select_end_time')->nullable();
            $table->string('select_full_time')->nullable();
            $table->longText('select_topic')->nullable();
            $table->foreignId('select_activity_id')->references('activity_id')->on('workshops');
            $table->integer('select_language')->nullable();
            $table->integer('closed')->nullable();
            $table->integer('status')->nullable();
            $table->integer('expert_status')->nullable();
            $table->string('select_price')->nullable();
            $table->string('select_valuta')->nullable();
            $table->string('expert_price')->nullable();
            $table->string('expert_valuta')->nullable();
            $table->bigInteger('number_of_participants')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_cases');
    }
};
