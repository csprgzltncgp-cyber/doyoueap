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
        Schema::create('companies', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name')->comment('Cég neve');
            $table->unsignedBigInteger('country_id')->comment('Melyik országban elérhető a cég');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
