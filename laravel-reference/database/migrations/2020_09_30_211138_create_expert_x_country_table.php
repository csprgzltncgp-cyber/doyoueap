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
        Schema::create('expert_x_country', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();
            $table->foreign('expert_id')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_x_country');
    }
};
