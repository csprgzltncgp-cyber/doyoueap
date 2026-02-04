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
        Schema::create('close_telus_cases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('case_id')->references('id')->on('cases')->onDelete('cascade');
            $table->timestamp('closeable_after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_telus_cases');
    }
};
