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
        Schema::create('affiliate_search_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_search_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('value');
            $table->boolean('seen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_search_comments');
    }
};
