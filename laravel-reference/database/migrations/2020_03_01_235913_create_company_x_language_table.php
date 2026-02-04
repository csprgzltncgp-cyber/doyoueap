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
        Schema::create('company_x_language', function (Blueprint $table): void {
            $table->unsignedBigInteger('company_id')->comment('Megadja, hogy melyik céghez tartozik');
            $table->unsignedBigInteger('language_id')->comment('Megadja, hogy melyik nyelvhez tartozik');
            $table->primary(['company_id', 'language_id']);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_x_language');
    }
};
