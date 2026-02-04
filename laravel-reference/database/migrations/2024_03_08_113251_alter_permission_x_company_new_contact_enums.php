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
        // STEP 1. create online versions of skype enums
        DB::statement("
            ALTER TABLE permission_x_company 
            CHANGE contact contact ENUM(
                'skype',
                'phone',
                'personal',
                'skype-phone-personal',
                'skype-phone',
                'skype-personal',
                'phone-personal',
                'phone-email',
                'phone-online',

                'online',
                'phone',
                'personal',
                'online-phone-personal',
                'online-phone',
                'online-personal',
                'phone-personal',
                'phone-email',
                'phone-online'
            )"
        );

        // STEP 2. replace skype enums with online
        $permissions = DB::table('permission_x_company')->get();
        $permissions->each(function ($permission): void {
            $contact = match ($permission->contact) {
                'skype' => 'online',
                'skype-phone-personal' => 'online-phone-personal',
                'skype-phone' => 'online-phone',
                'skype-personal' => 'online-personal',
                default => $permission->contact
            };
            DB::table('permission_x_company')->where('id', $permission->id)->update(['contact' => $contact]);
        });

        // STEP 3. remove old enums and and video-chat enums
        DB::statement("
            ALTER TABLE permission_x_company 
            CHANGE contact contact ENUM(
                'online',
                'phone',
                'personal',
                'online-phone-personal',
                'online-phone',
                'online-personal',
                'phone-personal',
                'phone-email',
                'phone-online',

                'chat-video',
                'phone',
                'personal',
                'chat-video-phone-personal',
                'chat-video-phone',
                'chat-video-personal',
                'phone-personal',
                'phone-email',
                'phone-chat-video'
            )"
        );

        // STEP 4. replace online enums with chat-video
        $permissions = DB::table('permission_x_company')->get();
        $permissions->each(function ($permission): void {
            $contact = match ($permission->contact) {
                'online' => 'chat-video',
                'online-phone-personal' => 'chat-video-phone-personal',
                'online-phone' => 'chat-video-phone',
                'online-personal' => 'chat-video-personal',
                'phone-online' => 'phone-chat-video',
                default => $permission->contact
            };
            DB::table('permission_x_company')->where('id', $permission->id)->update(['contact' => $contact]);
        });

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
                'phone-chat-video'
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
