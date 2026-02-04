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
        if (! Schema::hasColumn('users', 'riport_language_id')) {
            Schema::table('users', function (Blueprint $table): void {
                //
                $table->unsignedBigInteger('riport_language_id')->after('country_id')->nullable();
                $table->foreign('riport_language_id')->references('id')->on('languages');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            //
        });
    }
};
