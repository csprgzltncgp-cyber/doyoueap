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
        Schema::table('orientations', function (Blueprint $table): void {
            $table->dropIndex('orientations_user_id_index');
            $table->dropIndex('orientations_country_id_index');
            $table->dropIndex('orientations_contact_holder_id_index');
            $table->dropIndex('orientations_company_id_index');
            $table->dropIndex('orientations_city_id_index');
        });

        Schema::rename('orientations', 'other_activity');

        Schema::table('other_activity', function (Blueprint $table): void {
            $table->index(['user_id']);
            $table->index(['company_id']);
            $table->index(['contact_holder_id']);
            $table->index(['country_id']);
            $table->index(['city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_activity', function (Blueprint $table): void {
            //
        });
    }
};
