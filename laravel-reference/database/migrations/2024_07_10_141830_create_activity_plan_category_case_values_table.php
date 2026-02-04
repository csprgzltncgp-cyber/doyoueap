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
        Schema::create('activity_plan_category_case_values', function (Blueprint $table): void {
            $table->id();
            // @phpstan-ignore-next-line
            $table->foreignId('activity_plan_category_case_id')->constrained()->cascadeOnDelete()->name('fk_apc_case_id');
            // @phpstan-ignore-next-line
            $table->foreignId('activity_plan_category_field_id')->constrained()->cascadeOnDelete()->name('fk_apc_field_id');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_plan_category_case_values');
    }
};
