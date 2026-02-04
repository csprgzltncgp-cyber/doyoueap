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
        Schema::table('riport_subcategories', function (Blueprint $table): void {
            //
            $table->boolean('summarize')->comment('Megadja, hogy a riport szerkesztésnél össze kell-e számol a megadott értékeket')->default(false)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riport_subcategories', function (Blueprint $table): void {
            //
        });
    }
};
