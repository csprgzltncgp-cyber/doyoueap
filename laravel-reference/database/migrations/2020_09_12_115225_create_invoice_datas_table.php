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
        Schema::create('invoice_datas', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy melyik felhasználóhoz tartozik');
            $table->string('name');
            $table->string('email');
            $table->string('account_number');
            $table->string('swift')->nullable();
            $table->string('bank_name');
            $table->string('bank_address');
            $table->string('destination_country');
            $table->string('currency');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_datas');
    }
};
