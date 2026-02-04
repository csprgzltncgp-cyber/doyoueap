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
        Schema::create('user_x_permission', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik szakértő lett hozzárendelve');
            $table->unsignedBigInteger('permission_id')->comment('Megadja, hogy melyik jogosultsághoz');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_x_permission');
    }
};
