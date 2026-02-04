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
        Schema::create('account_numbers', function (Blueprint $table): void {
            $table->id();
            $table->string('account_number');
            $table->string('currency');
            $table->unsignedBigInteger('cgp_data_id');
            $table->timestamps();

            if (config('app.env') == 'production') {
                $table->foreign('cgp_data_id')->references('id')->on('cgp_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_numbers');
    }
};
