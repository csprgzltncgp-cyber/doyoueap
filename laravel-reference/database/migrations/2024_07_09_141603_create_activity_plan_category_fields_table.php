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
        Schema::create('activity_plan_category_fields', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('activity_plan_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->boolean('is_highlighted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_plan_category_fields');
    }
};
