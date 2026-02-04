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
        Schema::create('workshop_case_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workshop_case_id')->comment('Megadja, hogy melyik workshop case-hez tartozik');
            $table->unsignedBigInteger('user_id')->comment('Megadja, hogy ki oké-zta le ezt a jelzést')->nullable();
            $table->enum('event', ['workshop_case_price_modified_by_admin', 'workshop_case_price_modified_by_expert', 'workshop_case_accepted_by_admin', 'workshop_case_denied_by_expert']);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('workshop_case_events', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('workshop_case_id')->references('id')->on('workshop_cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_case_events');
    }
};
