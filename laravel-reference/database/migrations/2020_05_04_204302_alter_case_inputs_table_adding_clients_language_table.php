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

        Schema::table('case_inputs', function (Blueprint $table): void {
            //
            DB::statement("ALTER TABLE case_inputs CHANGE COLUMN default_type default_type ENUM('company_chooser','case_creation_time','case_type','client_name','location','is_crisis','presenting_concern','clients_language') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_inputs', function (Blueprint $table): void {
            //
        });
    }
};
