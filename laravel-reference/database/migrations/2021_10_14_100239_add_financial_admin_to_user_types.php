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
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('admin', 'operator', 'expert', 'client', 'production_admin', 'translator', 'account_admin', 'financial_admin')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_types', function (Blueprint $table): void {
            //
        });
    }
};
