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
        Schema::table('orientation_events', function (Blueprint $table): void {
            $table->renameColumn('orientation_id', 'other_activity_id');
            $table->dropIndex('orientation_events_orientation_id_foreign');
        });

        Schema::rename('orientation_events', 'other_activity_events');

        Schema::table('other_activity_events', function (Blueprint $table): void {
            $table->index(['other_activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_activity_events', function (Blueprint $table): void {
            //
        });
    }
};
