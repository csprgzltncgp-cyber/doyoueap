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
        Schema::create('invoice_case_datas', function (Blueprint $table): void {
            $table->id();
            $table->string('case_identifier');
            $table->integer('consultations_count')->unsigned();
            $table->foreignId('expert_id')->constrained('users');
            $table->foreignId('invoice_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_case_data');
    }
};
