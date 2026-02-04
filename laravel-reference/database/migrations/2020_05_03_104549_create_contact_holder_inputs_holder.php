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
        Schema::create('contact_holder_inputs', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('selectable_id');
            $table->string('selectable_type');
            $table->unsignedBigInteger('contact_holder_id');
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contact_holder_id')->references('id')->on('contact_holders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_holder_inputs');
    }
};
