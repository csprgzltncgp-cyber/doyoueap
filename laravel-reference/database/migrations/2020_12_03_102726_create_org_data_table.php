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
        Schema::create('org_data', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('contact_holder')->references('id')->on('contact_holders');
            $table->integer('org_id')->nullable();
            $table->integer('workshops_number')->nullable();
            $table->integer('crisis_number')->nullable();
            $table->string('head_count')->nullable();
            $table->foreignId('company_id')->references('id')->on('companies');
            $table->foreignId('country_id')->references('id')->on('countries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('org_data');
    }
};
