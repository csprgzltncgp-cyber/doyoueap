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
        Schema::create('activity_plan_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('activity_plan_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('activity_plan_memberable_id');
            $table->string('activity_plan_memberable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_plan_members');
    }
};
