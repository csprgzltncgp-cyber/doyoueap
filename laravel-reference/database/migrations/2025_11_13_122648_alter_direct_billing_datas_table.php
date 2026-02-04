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
        Schema::table('direct_billing_datas', function (Blueprint $table): void {
            $table->boolean('inside_eu')->default(false)->after('vat_rate');
            $table->boolean('outside_eu')->default(false)->after('inside_eu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
