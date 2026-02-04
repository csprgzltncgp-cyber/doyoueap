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
        Schema::create('expert_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone_prefix')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('post_code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('street')->nullable();
            $table->tinyInteger('street_suffix')->nullable();
            $table->string('house_number')->nullable();
            $table->boolean('required_documents')->default(false);
            $table->boolean('completed_first')->default(false);
            $table->timestamps();

            if (config('app.env') != 'local') {
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('country_id')->references('id')->on('countries');
                $table->foreign('city_id')->references('id')->on('cities');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_data');
    }
};
