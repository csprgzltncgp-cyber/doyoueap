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
        Schema::rename('inventory', 'assets');
        Schema::rename('inventory_types', 'asset_types');
        Schema::rename('inventory_owners', 'asset_owners');

        Schema::table('assets', function (Blueprint $table): void {
            $table->renameColumn('inventory_type_id', 'asset_type_id');
            $table->dropForeign('inventory_owner_id_foreign');
            $table->foreign('owner_id')->references('id')->on('asset_owners');
            $table->dropForeign('inventory_inventory_type_id_foreign');
            $table->foreign('asset_type_id')->references('id')->on('asset_types');
        });

        Schema::table('asset_owners', function (Blueprint $table): void {
            $table->dropForeign('inventory_owners_country_id_foreign');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table): void {
            //
        });
    }
};
