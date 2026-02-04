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
        Schema::create('invoice_orientation_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('orientation_id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('activity_id');
            $table->integer('price');
            $table->string('currency');
            $table->timestamps();

            if (config('app.env') !== 'local') {
                $table->foreign('expert_id')->references('id')->on('users');
                $table->foreign('orientation_id')->references('id')->on('orientations');
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_orientation_data');
    }
};
