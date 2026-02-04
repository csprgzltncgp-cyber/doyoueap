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
        Schema::table('live_webinars', function (Blueprint $table): void {
            $table->string('zoom_meeting_id')->nullable()->after('image');
            $table->string('zoom_meeting_uuid')->nullable()->after('zoom_meeting_id');
            $table->text('zoom_host_start_url')->nullable()->after('zoom_meeting_uuid');
            $table->text('zoom_join_url')->nullable()->after('zoom_host_start_url');
            $table->string('zoom_passcode')->nullable()->after('zoom_join_url');
            $table->string('zoom_sdk_role')->default('host')->after('zoom_passcode');
            $table->timestamp('zoom_meeting_started_at')->nullable()->after('zoom_sdk_role');
            $table->timestamp('zoom_meeting_ended_at')->nullable()->after('zoom_meeting_started_at');
            $table->timestamp('recording_downloaded_at')->nullable()->after('zoom_meeting_ended_at');
            $table->text('recording_download_path')->nullable()->after('recording_downloaded_at');
            $table->string('recording_status')->default('pending')->after('recording_download_path');

            $table->index('zoom_meeting_id');
            $table->index('zoom_meeting_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_webinars', function (Blueprint $table): void {
            $table->dropIndex(['zoom_meeting_id']);
            $table->dropIndex(['zoom_meeting_uuid']);

            $table->dropColumn([
                'zoom_meeting_id',
                'zoom_meeting_uuid',
                'zoom_host_start_url',
                'zoom_join_url',
                'zoom_passcode',
                'zoom_sdk_role',
                'zoom_meeting_started_at',
                'zoom_meeting_ended_at',
                'recording_downloaded_at',
                'recording_download_path',
                'recording_status',
            ]);
        });
    }
};
