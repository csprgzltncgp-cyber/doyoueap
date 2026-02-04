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
        Schema::create('deutsche_telekom_emails', function (Blueprint $table): void {
            $table->id();
            $table->string('email');
            $table->unsignedBigInteger('case_id_1');
            $table->unsignedBigInteger('case_id_2')->nullable();
            $table->unsignedBigInteger('case_id_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deutsche_telekom_emails');
    }
};
