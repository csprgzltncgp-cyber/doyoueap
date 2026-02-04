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
            $table->enum('display_format', ['icon', 'table'])->after('type')->comment('Milyen formában jelenítjük meg a riportnál?');
            $table->tinyInteger('chart')->default(0)->after('display_format')->comment('Kell-e diagram a riportnál?');
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
