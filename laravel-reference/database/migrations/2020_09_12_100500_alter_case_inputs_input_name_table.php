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
        Schema::table('case_inputs', function (Blueprint $table): void {
            //
            $table->string('input_id')->after('default_type')->comment('Arra kell, hogy azonosítani tudjunk bizonyos inputokat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_inputs', function (Blueprint $table): void {
            //
        });
    }
};
