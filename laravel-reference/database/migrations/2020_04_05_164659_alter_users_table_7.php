<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            // \DB::statement("ALTER TABLE cases CHANGE COLUMN status status ENUM('opened','assigned_to_expert','employee_contacted','client_unreachable','confirmed') NOT NULL DEFAULT 'opened'");
            DB::statement("ALTER TABLE users CHANGE COLUMN type type ENUM('admin', 'operator', 'expert', 'client') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            //
        });
    }
};
