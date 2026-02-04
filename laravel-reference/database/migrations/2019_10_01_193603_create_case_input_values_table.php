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
        Schema::create('case_input_values', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('value')->comment('Az érték');
            $table->unsignedBigInteger('case_input_id')->comment('Megadja, hogy melyik input-hoz tartozik az adott input érték');
            $table->boolean('is_default')->default(false)->comment('Megadja, hogy adott input érték-e a default érték megjelenítéskor');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('case_input_id')->references('id')->on('case_inputs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_input_values');
    }
};
