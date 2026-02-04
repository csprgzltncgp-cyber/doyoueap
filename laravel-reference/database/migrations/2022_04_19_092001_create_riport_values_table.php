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
        Schema::create('riport_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('riport_id')->constrained();
            $table->foreignId('country_id')->constrained();
            $table->integer('type');
            $table->string('value');
            $table->uuid('connection_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riport_values');
    }
};
