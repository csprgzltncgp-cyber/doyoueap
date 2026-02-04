<?php

namespace App\Jobs;

use RuntimeException;
use App\Models\LiveWebinar;
use App\Services\Vimeo\VimeoUploadService;
use App\Services\Zoom\ZoomMeetingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessLiveWebinarRecording implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const STATUS_PENDING = 'pending';

    public const STATUS_DOWNLOADING = 'downloading';

    public const STATUS_UPLOADING = 'uploading';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_FAILED = 'failed';

    public int $tries = 5;

    public int $timeout = 7200; // 2 hours for large video uploads

    public function __construct(public int $liveWebinarId) {}

    public function handle(
        ZoomMeetingService $zoomMeetingService,
        VimeoUploadService $vimeoUploadService
    ): void {
        $webinar = LiveWebinar::query()->find($this->liveWebinarId);

        if (! $webinar || $webinar->vimeo_video_url) {
            return;
        }

        if (! $webinar->zoom_meeting_id && ! $webinar->zoom_meeting_uuid) {
            $this->transitionStatus($webinar, self::STATUS_FAILED);

            return;
        }
        $this->transitionStatus($webinar, self::STATUS_DOWNLOADING);

        try {
            $recordings = $zoomMeetingService->listRecordings($webinar);
        } catch (Throwable $exception) {
            Log::warning('Zoom recordings not ready', [
                'live_webinar_id' => $webinar->id,
                'attempt' => $this->attempts(),
                'error' => $exception->getMessage(),
            ]);

            $this->retryLater($webinar);

            return;
        }

        $primaryRecording = $this->findPrimaryRecording(collect($recordings['recording_files'] ?? []));

        if (! $primaryRecording) {
            $this->retryLater($webinar);

            return;
        }

        try {
            $storagePath = $this->downloadRecording($zoomMeetingService, $webinar, $primaryRecording);
        } catch (Throwable $exception) {
            Log::warning('Unable to download Zoom recording', [
                'live_webinar_id' => $webinar->id,
                'recording_id' => Arr::get($primaryRecording, 'id'),
                'error' => $exception->getMessage(),
            ]);

            $this->retryLater($webinar);

            return;
        }

        $absolutePath = Storage::disk('local')->path($storagePath);

        $this->transitionStatus($webinar, self::STATUS_UPLOADING);

        try {
            $uploadResult = $vimeoUploadService->uploadRecording($absolutePath, $webinar);
        } catch (Throwable $exception) {
            Log::error('Unable to upload webinar recording to Vimeo', [
                'live_webinar_id' => $webinar->id,
                'error' => $exception->getMessage(),
            ]);

            // Keep the local file for retry - only delete on permanent failure
            if ($this->attempts() >= $this->tries) {
                Storage::disk('local')->delete($storagePath);
            }

            throw $exception;
        }

        // Delete local file after successful Vimeo upload
        Storage::disk('local')->delete($storagePath);

        // Delete Zoom recording AFTER successful Vimeo upload to prevent data loss
        $this->attemptRecordingDeletion($zoomMeetingService, $webinar, Arr::get($primaryRecording, 'id'));

        $vimeo_url = $uploadResult->publicUrl;
        // Convert vimeo.com/{id} to player.vimeo.com/video/{id}
        if (preg_match('/vimeo\.com\/(\d+)/', $vimeo_url, $matches)) {
            $embed_url = "https://player.vimeo.com/video/{$matches[1]}";
        } else {
            $embed_url = $vimeo_url;
        }

        $webinar->update([
            'vimeo_video_url' => $embed_url,
            'recording_status' => self::STATUS_ARCHIVED,
            'recording_archived_at' => now(),
        ]);
    }

    protected function findPrimaryRecording(Collection $files): ?array
    {
        // First try to find a completed MP4 (video) recording
        $mp4Recording = $files->first(fn(array $file): bool => ($file['file_type'] ?? '') === 'MP4'
            && ($file['status'] ?? '') === 'completed');

        if ($mp4Recording) {
            return $mp4Recording;
        }

        // Fall back to M4A (audio-only) if no MP4 is available
        return $files->first(fn(array $file): bool => ($file['file_type'] ?? '') === 'M4A'
            && ($file['status'] ?? '') === 'completed');
    }

    protected function downloadRecording(
        ZoomMeetingService $zoomMeetingService,
        LiveWebinar $webinar,
        array $recordingFile
    ): string {
        $downloadUrl = Arr::get($recordingFile, 'download_url');

        if (! $downloadUrl) {
            throw new RuntimeException('Recording download URL is missing.');
        }

        $fileType = strtolower(Arr::get($recordingFile, 'file_type', 'mp4'));
        $extension = $fileType === 'm4a' ? 'm4a' : 'mp4';
        $fileName = Arr::get($recordingFile, 'id', uniqid('recording_', true));
        $storagePath = sprintf('live-webinar-recordings/%s/%s.%s', $webinar->id, $fileName, $extension);

        $absolutePath = Storage::disk('local')->path($storagePath);

        // Check if file already exists from a previous attempt (idempotency)
        if (file_exists($absolutePath) && filesize($absolutePath) > 0) {
            Log::info('Zoom recording already downloaded, skipping download', [
                'live_webinar_id' => $webinar->id,
                'path' => $storagePath,
            ]);

            return $storagePath;
        }

        // Ensure directory exists
        $directory = dirname($absolutePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Use streaming download to handle large files without memory issues
        $zoomMeetingService->downloadRecordingToFile($downloadUrl, $absolutePath);

        return $storagePath;
    }

    protected function retryLater(LiveWebinar $webinar): void
    {
        if ($this->attempts() >= $this->tries) {
            $this->transitionStatus($webinar, self::STATUS_FAILED);

            return;
        }

        $this->release(now()->addMinutes(5));
    }

    protected function transitionStatus(LiveWebinar $webinar, string $status): void
    {
        if ($webinar->recording_status === $status) {
            return;
        }

        $webinar->update(['recording_status' => $status]);
    }

    protected function attemptRecordingDeletion(
        ZoomMeetingService $zoomMeetingService,
        LiveWebinar $webinar,
        ?string $recordingId
    ): void {
        if (! $recordingId) {
            return;
        }

        try {
            $zoomMeetingService->deleteRecordingFile($webinar, $recordingId);

            return;
        } catch (RequestException $exception) {
            $status = $exception->response->status();

            if ($status === 404) {
                $this->deleteAllRecordings($zoomMeetingService, $webinar);

                return;
            }

            Log::warning('Unable to delete Zoom recording file', [
                'live_webinar_id' => $webinar->id,
                'recording_id' => $recordingId,
                'error' => $exception->getMessage(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Unable to delete Zoom recording file', [
                'live_webinar_id' => $webinar->id,
                'recording_id' => $recordingId,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    protected function deleteAllRecordings(
        ZoomMeetingService $zoomMeetingService,
        LiveWebinar $webinar
    ): void {
        try {
            $zoomMeetingService->deleteAllRecordings($webinar);
        } catch (Throwable $exception) {
            Log::warning('Unable to delete any Zoom recordings for webinar', [
                'live_webinar_id' => $webinar->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
