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
        Schema::create('affiliate_searches', function (Blueprint $table): void {
            $table->id();
            $table->text('description');
            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('to_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('permission_id');
            $table->tinyInteger('status');
            $table->tinyInteger('deadline_type');
            $table->date('deadline');
            $table->boolean('completed')->default(false);

            if (config('app.env') != 'local') {
                $table->foreign('from_id')->references('id')->on('users');
                $table->foreign('to_id')->references('id')->on('users');
                $table->foreign('country_id')->references('id')->on('countries');
                $table->foreign('city_id')->references('id')->on('cities');
                $table->foreign('permission_id')->references('id')->on('permissions');
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_searches');
    }
};
