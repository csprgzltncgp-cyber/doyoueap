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
        Schema::enableForeignKeyConstraints();
        Schema::create('crisis_case_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('crisis_case_id')->comment('Megadja, hogy melyik crisis case-hez tartozik');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy ki oké-zta le ezt a jelzést')->nullable();
            $table->enum('event', ['crisis_case_price_modified_by_admin', 'crisis_case_price_modified_by_expert', 'crisis_case_accepted_by_admin', 'crisis_case_denied_by_expert']);
            $table->timestamps();
        });

        Schema::table('crisis_case_events', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('crisis_case_id')->references('id')->on('crisis_cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crisis_case_events');
    }
};
