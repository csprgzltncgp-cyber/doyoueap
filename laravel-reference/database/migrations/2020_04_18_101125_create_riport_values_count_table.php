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
        Schema::create('riport_values_count', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('riport_id');
            $table->unsignedBigInteger('case_input_values_id');
            $table->integer('count');
            $table->timestamps();

            $table->foreign('case_input_values_id')->references('id')->on('case_input_values');
            $table->foreign('riport_id')->references('id')->on('riports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riport_values_count');
    }
};
