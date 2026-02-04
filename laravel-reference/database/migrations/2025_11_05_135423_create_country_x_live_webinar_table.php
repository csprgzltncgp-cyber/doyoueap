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
        Schema::create('country_x_live_webinar', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('live_webinar_id');
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('live_webinar_id')->references('id')->on('live_webinars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_x_live_webinar');
    }
};
