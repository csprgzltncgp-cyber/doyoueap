<?php

namespace App\Services\Vimeo;

use Throwable;
use App\Models\LiveWebinar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Vimeo\Vimeo;

class VimeoUploadService
{
    /**
     * Chunk size for TUS uploads (20MB).
     * Must be smaller than PHP memory_limit to avoid memory exhaustion.
     */
    private const UPLOAD_CHUNK_SIZE = 20 * 1024 * 1024;

    public function __construct(private Vimeo $client) {}

    public function uploadRecording(string $path, LiveWebinar $webinar): VimeoUploadResult
    {
        $title = trim($webinar->topic ?? 'Live Webinar Recording');
        $recordedAt = $webinar->from->timezone(config('app.timezone', 'UTC'));
        $fileSize = @filesize($path);

        if (! $fileSize) {
            throw new RuntimeException('Downloaded recording is empty, aborting Vimeo upload.');
        }

        $payload = [
            'name' => $title !== '' ? $title : 'Live Webinar Recording',
            'description' => sprintf(
                'Recorded on %s%s',
                optional($recordedAt)->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                $webinar->expert ? ' by '.$webinar->expert->name : ''
            ),
        ];

        $privacyOptions = array_filter((array) data_get(config('services.vimeo', []), 'upload_options.privacy'));

        if ($privacyOptions !== []) {
            $payload['privacy'] = $privacyOptions;
        }

        Log::info('Starting Vimeo upload', [
            'live_webinar_id' => $webinar->id,
            'file_size' => $fileSize,
            'file_size_mb' => round($fileSize / 1024 / 1024, 2),
            'chunk_size_mb' => self::UPLOAD_CHUNK_SIZE / 1024 / 1024,
        ]);

        try {
            // Use smaller chunk size (20MB) to avoid memory exhaustion
            // Default is 100MB which exceeds typical PHP memory limits
            $uri = $this->client->upload($path, $payload, self::UPLOAD_CHUNK_SIZE);
        } catch (Throwable $exception) {
            Log::error('Unable to initiate Vimeo upload', [
                'live_webinar_id' => $webinar->id,
                'payload' => $payload,
                'file_size' => $fileSize,
                'error' => $exception->getMessage(),
                'exception_class' => $exception::class,
            ]);

            throw $exception;
        }

        Log::info('Vimeo upload completed', [
            'live_webinar_id' => $webinar->id,
            'vimeo_uri' => $uri,
        ]);

        $details = $this->client->request($uri, [], 'GET');

        if (($details['status'] ?? 500) >= 400) {
            throw new RuntimeException('Unable to fetch uploaded Vimeo video details.');
        }

        $body = $details['body'] ?? [];
        $link = $body['link'] ?? $this->inferPublicUrl($uri);

        if (! $link) {
            throw new RuntimeException('Vimeo upload completed but no public link was returned.');
        }

        return new VimeoUploadResult($uri, $link);
    }

    public function deleteVideo(?string $uri): void
    {
        if (! $uri) {
            return;
        }

        $response = $this->client->request($uri, [], 'DELETE');

        if ($response['status'] >= 400) {
            Log::warning('Unable to delete Vimeo video', [
                'uri' => $uri,
                'status' => $response['status'],
            ]);
        }
    }

    protected function inferPublicUrl(string $uri): ?string
    {
        if (! Str::startsWith($uri, '/videos/')) {
            return null;
        }

        return 'https://vimeo.com/'.Str::after($uri, '/videos/');
    }
}
