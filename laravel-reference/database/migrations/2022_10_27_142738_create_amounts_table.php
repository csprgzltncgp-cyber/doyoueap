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
        Schema::create('amounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_item_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('value', 15, 2)->nullable();
            $table->boolean('is_changing')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amounts');
    }
};
