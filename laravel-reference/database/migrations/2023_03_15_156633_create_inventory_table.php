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
        Schema::create('inventory', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('asset_type_id');
            $table->string('own_id');
            $table->string('cgp_id');
            $table->string('name');
            $table->date('date_of_purchase');
            $table->string('phone_num')->nullable();
            $table->string('pin')->nullable();
            $table->string('provider')->nullable();
            $table->string('package')->nullable();

            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('asset_owners');
            $table->foreign('asset_type_id')->references('id')->on('asset_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
