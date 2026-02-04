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
        Schema::create('affiliate_search_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_search_id')->constrained();
            $table->string('filename');
            $table->string('path');
            $table->timestamps();

            if (config('app.env') != 'local') {
                $table->foreign('affiliate_search_id')->references('id')->on('affiliate_searches')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_search_attachments');
    }
};
