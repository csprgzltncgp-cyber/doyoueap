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
        Schema::create('completion_certificates', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('direct_invoice_id')->constrained();
            $table->string('filename')->nullable();
            $table->boolean('with_header')->default(false);
            $table->string('path')->nullable();

            $table->timestamp('printed_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completion_certificates');
    }
};
