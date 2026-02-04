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
        Schema::create('notification_group_targets_countries', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('group_target_id');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('group_target_id')->references('id')->on('notification_group_targets');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_group_targets_countries');
    }
};
