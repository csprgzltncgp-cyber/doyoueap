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
        Schema::create('live_webinars', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('permission_id')->references('id')->on('permissions');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('topic');
            $table->timestamp('from');
            $table->timestamp('to');
            $table->smallInteger('duration');
            $table->longText('description');
            $table->unsignedBigInteger('language_id');
            $table->string('currency');
            $table->integer('price');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_webinars');
    }
};
