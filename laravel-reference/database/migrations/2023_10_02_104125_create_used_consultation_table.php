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
        Schema::create('used_consultations', function (Blueprint $table): void {
            $table->id();
            $table->boolean('cgp_employee');
            $table->string('type');
            $table->smallInteger('number_of_consultations')->nullable();
            $table->float('consultation_average')->nullable();
            $table->float('total_percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_consultation');
    }
};
