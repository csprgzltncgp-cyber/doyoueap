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
        Schema::create('uploads', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->enum('type', ['elegedettsegi_kerdoiv']);
            $table->string('url');
            $table->unsignedBigInteger('case_id')->comment('Megadja, hogy melyik esethez tartozik');
            $table->timestamps();

            $table->foreign('case_id')->references('id')->on('cases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
