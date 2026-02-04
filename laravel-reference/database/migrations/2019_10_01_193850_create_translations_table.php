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
        Schema::create('translations', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('value');
            $table->unsignedBigInteger('language_id')->comment('Megadja, hogy melyik nyelvhez tartozik az adott fordítás');
            $table->unsignedBigInteger('translatable_id');
            $table->string('translatable_type');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
