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
        Schema::rename('user_x_contact_holder', 'user_x_contract_holder');

        Schema::table('user_x_contract_holder', function (Blueprint $table): void {
            $table->renameColumn('contact_holder_id', 'contract_holder_id');
            $table->dropForeign('user_x_contact_holder_contact_holder_id_foreign');
            $table->foreign('contract_holder_id')->references('id')->on('contract_holders');
            $table->dropForeign('user_x_contact_holder_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_x_contract_holder', function (Blueprint $table): void {
            //
        });
    }
};
