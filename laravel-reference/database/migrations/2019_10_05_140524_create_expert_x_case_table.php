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
        Schema::create('expert_x_case', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik szakértő lett hozzárendelve');
            $table->unsignedBigInteger('case_id')->comment('Megadja, hogy melyik eset');
            $table->tinyInteger('accepted')->default(-1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('case_id')->references('id')->on('cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_x_case');
    }
};
