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
        Schema::create('invoice_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->comment('Megadja, hogy melyik számlához tartozik');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy ki oké-zta le ezt a jelzést')->nullable();
            $table->enum('event', ['invoice_expired_and_not_paid', 'invoice_paid']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_events');
    }
};
