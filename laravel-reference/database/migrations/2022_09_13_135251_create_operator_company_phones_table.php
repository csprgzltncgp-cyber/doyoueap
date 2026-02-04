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
        Schema::create('operator_company_phones', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('operator_data_id');
            $table->string('phone');
            $table->timestamps();

            if (config('app.env') != 'local') {
                $table->foreign('operator_data_id')->references('id')->on('operator_datas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_company_phones');
    }
};
