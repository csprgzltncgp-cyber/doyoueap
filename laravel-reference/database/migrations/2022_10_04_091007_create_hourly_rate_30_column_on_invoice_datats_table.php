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
        Schema::table('invoice_datas', function (Blueprint $table): void {
            $table->integer('hourly_rate_30')->nullable()->after('hourly_rate_50');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
