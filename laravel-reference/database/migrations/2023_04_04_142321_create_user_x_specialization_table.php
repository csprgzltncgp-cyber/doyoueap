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
        Schema::create('user_x_specialization', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik szakértő lett hozzárendelve');
            $table->unsignedBigInteger('specializations_id')->comment('Megadja, hogy melyik specializációhoz');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('specializations_id')->references('id')->on('specializations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_x_specialization');
    }
};
