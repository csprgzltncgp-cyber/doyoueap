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
        Schema::table('invoice_orientation_datas', function (Blueprint $table): void {
            $table->renameColumn('orientation_id', 'other_activity_id');
            $table->dropIndex('invoice_orientation_datas_orientation_id_foreign');
            $table->dropIndex('invoice_orientation_datas_invoice_id_foreign');
            $table->dropIndex('invoice_orientation_datas_expert_id_foreign');
        });

        Schema::rename('invoice_orientation_datas', 'invoice_other_activity_datas');

        Schema::table('invoice_other_activity_datas', function (Blueprint $table): void {
            $table->index(['other_activity_id']);
            $table->index(['invoice_id']);
            $table->index(['expert_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_other_activity_datas', function (Blueprint $table): void {
            //
        });
    }
};
