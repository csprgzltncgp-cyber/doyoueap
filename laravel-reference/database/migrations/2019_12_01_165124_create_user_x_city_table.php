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
        Schema::create('user_x_city', function (Blueprint $table): void {
            $table->unsignedBigInteger('city_id')->comment('Megadja, hogy melyik városhoz tartozik');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik userhez tartozik');

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_x_city');
    }
};
