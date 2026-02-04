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
        Schema::create('invoice_crisis_datas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('crisis_case_id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('activity_id');
            $table->integer('price');
            $table->string('currency');
            $table->timestamps();

            if (config('app.env') !== 'local') {
                $table->foreign('expert_id')->references('id')->on('users');
                $table->foreign('crisis_case_id')->references('id')->on('crisis_cases');
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_crisis_data');
    }
};
