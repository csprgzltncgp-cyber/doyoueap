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
        Schema::create('invoice_data_changes', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('attribute');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik felhasználóhoz tartozik');
            $table->unsignedBigInteger('seen_by')->comment('Megadja, hogy melyik felhasználó látta a változást')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seen_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_data_changes');
    }
};
