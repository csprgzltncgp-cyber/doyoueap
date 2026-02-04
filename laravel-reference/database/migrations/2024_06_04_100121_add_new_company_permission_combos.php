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
        // STEP 5. remove old/unused enums
        DB::statement("
            ALTER TABLE permission_x_company 
            CHANGE contact contact ENUM (
                'chat-video',
                'phone',
                'personal',
                'chat-video-phone-personal',
                'chat-video-phone',
                'chat-video-personal',
                'phone-personal',
                'phone-email',
                'phone-chat-video',
                'video-phone',
                'video-phone-personal',
                'video'
            )"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
