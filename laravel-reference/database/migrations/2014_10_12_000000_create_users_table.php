ű<?php

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
        Schema::create('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name')->comment('Felhasználó neve');
            $table->string('email')->unique()->comment('Felhasználó email címe');
            $table->string('username')->unique()->comment('Felhasználónév');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('Felhasználó jelszava')->nullable();
            $table->enum('type', ['operator', 'expert', 'admin'])->comment('Felhasználó jogosultsága');
            $table->unsignedBigInteger('language_id')->comment('Megadja, hogy milyen nyelven használja a felhasználó az admint');
            $table->unsignedBigInteger('country_id')->comment('Megadja, hogy melyik országhoz tartozik a felhasználó')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('super_user')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
