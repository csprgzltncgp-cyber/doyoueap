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
        Schema::table('invoice_x_case', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->after('case_id')->comment('Megadja, hogy melyik felhasználóhoz tartozik');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_x_case', function (Blueprint $table): void {
            //
        });
    }
};
