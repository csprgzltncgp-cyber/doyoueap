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
        Schema::create('consultations', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('case_id')->comment('Megadja, hogy melyik esethez tartozik a konzultáció');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik szakértőt rendelték a konzultációhoz');
            $table->unsignedBigInteger('permission_id')->comment('Megadja, hogy melyik jogosultság lett felhasználva');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('case_id')->references('id')->on('cases');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
