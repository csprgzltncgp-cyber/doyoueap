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
        Schema::create('orientations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->index()->constrained();
            $table->foreignId('contact_holder_id')->index()->constrained();
            $table->foreignId('user_id')->nullable()->index()->constrained();
            $table->foreignId('country_id')->nullable()->index()->constrained();
            $table->foreignId('city_id')->nullable()->index()->constrained();
            $table->string('activity_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('company_price')->nullable();
            $table->string('company_currency')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->integer('user_price')->nullable();
            $table->string('user_currency')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('language')->nullable();
            $table->integer('participants')->nullable();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientations');
    }
};
