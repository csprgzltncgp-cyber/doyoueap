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
        Schema::create('wos_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->unsignedInteger('answer_1');
            $table->unsignedInteger('answer_2');
            $table->unsignedInteger('answer_3');
            $table->unsignedInteger('answer_4');
            $table->unsignedInteger('answer_5');
            $table->unsignedInteger('answer_6');
            $table->timestamp('created_at');

            $table->foreign('case_id')->references('id')->on('cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wos_answers');
    }
};
