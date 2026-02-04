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
        Schema::create('case_values', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('case_id')->comment('Megadja, hogy melyik esethez tartozik');
            $table->unsignedBigInteger('case_input_id')->comment('Megadja, hogy melyik case input-hoz tartozik');
            $table->string('value');

            $table->timestamps();

            $table->foreign('case_id')->references('id')->on('cases');
            $table->foreign('case_input_id')->references('id')->on('case_inputs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_values');
    }
};
