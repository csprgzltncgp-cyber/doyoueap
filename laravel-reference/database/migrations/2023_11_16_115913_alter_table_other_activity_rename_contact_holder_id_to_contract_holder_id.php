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
        Schema::table('other_activity', function (Blueprint $table): void {
            $table->renameColumn('contact_holder_id', 'contract_holder_id');

            $table->dropIndex('other_activity_contact_holder_id_index');
            $table->index(['contract_holder_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_holder_id', function (Blueprint $table): void {
            //
        });
    }
};
