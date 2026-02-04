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
        DB::statement("
            ALTER TABLE case_inputs
            MODIFY COLUMN default_type
            ENUM(
                'company_chooser',
                'case_creation_time',
                'case_type',
                'client_name',
                'location',
                'is_crisis',
                'presenting_concern',
                'clients_language',
                'client_email',
                'case_language_skill',
                'case_specialization'
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
