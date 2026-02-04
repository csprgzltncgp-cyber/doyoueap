<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE crisis_cases MODIFY COLUMN select_language VARCHAR(255)');
        DB::statement('ALTER TABLE workshop_cases MODIFY COLUMN select_language VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE crisis_cases MODIFY COLUMN select_language INTEGER');
        DB::statement('ALTER TABLE workshop_cases MODIFY COLUMN select_language INTEGER');
    }
};
