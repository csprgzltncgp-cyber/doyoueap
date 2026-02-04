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
        Schema::create('invoice_live_webinar_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('live_webinar_id');
            $table->string('activity_id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->integer('price');
            $table->string('currency');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_live_webinar_datas');
    }
};
