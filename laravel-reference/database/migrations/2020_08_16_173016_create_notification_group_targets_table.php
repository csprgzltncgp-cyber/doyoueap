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
        Schema::create('notification_group_targets', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_id');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('notification_id')->references('id')->on('notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_group_targets');
    }
};
