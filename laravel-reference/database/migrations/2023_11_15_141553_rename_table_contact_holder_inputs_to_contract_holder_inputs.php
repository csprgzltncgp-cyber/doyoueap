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
        Schema::rename('contact_holder_inputs', 'contract_holder_inputs');

        Schema::table('contract_holder_inputs', function (Blueprint $table): void {
            $table->renameColumn('contact_holder_id', 'contract_holder_id');
            $table->dropForeign('contact_holder_inputs_contact_holder_id_foreign');
            $table->foreign('contract_holder_id')->references('id')->on('contract_holders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_holder_inputs', 'contract_holder_inputs');
    }
};
