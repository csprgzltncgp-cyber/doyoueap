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
        Schema::create('workshop_feedback', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('workshop_case_id');
            $table->unsignedTinyInteger('question_1');
            $table->unsignedTinyInteger('question_2');
            $table->unsignedTinyInteger('question_3');
            $table->unsignedTinyInteger('question_4');
            $table->unsignedTinyInteger('question_5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_feedback');
    }
};
