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
        Schema::table('riport_categories', function (Blueprint $table): void {
            //
            $table->tinyInteger('sort')->after('name');
            $table->enum('type', ['number_of_cases'])->nullable()->default(null)->after('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riport_categories', function (Blueprint $table): void {
            //
        });
    }
};
