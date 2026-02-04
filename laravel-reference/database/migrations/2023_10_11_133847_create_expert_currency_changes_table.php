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
        Schema::create('expert_currency_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('registered_seat');
            $table->string('registration_number');
            $table->string('tax_number');
            $table->string('represented_by');
            $table->string('hourly_rate_30_currency')->nullable();
            $table->string('hourly_rate_50_currency')->nullable();
            $table->string('hourly_rate_30')->nullable();
            $table->string('hourly_rate_50')->nullable();
            $table->string('document')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_currency_changes');
    }
};
