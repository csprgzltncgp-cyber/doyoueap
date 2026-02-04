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
        Schema::create('affiliate_search_completion_points', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('affiliate_search_id');
            $table->tinyInteger('type');
            $table->timestamps();

            if (config('app.env') != 'local') {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('affiliate_search_id')->references('id')->on('affiliate_searches');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_search_completion_points');
    }
};
