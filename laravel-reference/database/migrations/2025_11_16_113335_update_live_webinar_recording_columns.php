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
        Schema::table('live_webinars', function (Blueprint $table): void {
            if (Schema::hasColumn('live_webinars', 'recording_download_path')) {
                $table->dropColumn('recording_download_path');
            }

            if (Schema::hasColumn('live_webinars', 'recording_downloaded_at')) {
                $table->dropColumn('recording_downloaded_at');
            }
        });

        Schema::table('live_webinars', function (Blueprint $table): void {
            if (! Schema::hasColumn('live_webinars', 'vimeo_video_url')) {
                $table->text('vimeo_video_url')->nullable()->after('recording_status');
            }

            if (! Schema::hasColumn('live_webinars', 'recording_archived_at')) {
                $table->timestamp('recording_archived_at')->nullable()->after('vimeo_video_url');
            }

            $table->string('recording_status')->default('pending')->change();
        });

        DB::table('live_webinars')
            ->whereNull('recording_status')
            ->update(['recording_status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_webinars', function (Blueprint $table): void {
            if (Schema::hasColumn('live_webinars', 'recording_archived_at')) {
                $table->dropColumn('recording_archived_at');
            }

            if (Schema::hasColumn('live_webinars', 'vimeo_video_url')) {
                $table->dropColumn('vimeo_video_url');
            }
        });

        Schema::table('live_webinars', function (Blueprint $table): void {
            if (! Schema::hasColumn('live_webinars', 'recording_downloaded_at')) {
                $table->timestamp('recording_downloaded_at')->nullable()->after('zoom_meeting_ended_at');
            }

            if (! Schema::hasColumn('live_webinars', 'recording_download_path')) {
                $table->text('recording_download_path')->nullable()->after('recording_downloaded_at');
            }

            $table->string('recording_status')->default('pending')->change();
        });
    }
};
