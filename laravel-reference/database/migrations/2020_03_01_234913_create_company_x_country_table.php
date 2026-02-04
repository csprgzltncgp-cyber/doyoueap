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
        Schema::create('company_x_country', function (Blueprint $table): void {
            $table->unsignedBigInteger('company_id')->comment('Megadja, hogy melyik céghez tartozik');
            $table->unsignedBigInteger('country_id')->comment('Megadja, hogy melyik országhoz tartozik');
            $table->primary(['company_id', 'country_id']);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_x_country');
    }
};
