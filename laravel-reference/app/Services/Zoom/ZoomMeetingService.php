<?php

namespace App\Services\Zoom;

use App\Models\LiveWebinar;
use Exception;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class ZoomMeetingService
{
    public function __construct(
        protected ZoomOAuthClient $oauthClient,
        protected HttpFactory $http
    ) {}

    public function syncMeeting(LiveWebinar $webinar): LiveWebinar
    {
        $payload = $webinar->zoom_meeting_id
            ? $this->updateMeeting($webinar)
            : $this->createMeeting($webinar);

        $webinar->fill([
            'zoom_meeting_id' => (string) Arr::get($payload, 'id'),
            'zoom_meeting_uuid' => Arr::get($payload, 'uuid'),
            'zoom_host_start_url' => Arr::get($payload, 'start_url'),
            'zoom_join_url' => $this->webClientJoinUrl(
                Arr::get($payload, 'join_url'),
                (string) Arr::get($payload, 'id')
            ),
            'zoom_passcode' => Arr::get($payload, 'password'),
            'zoom_sdk_role' => $webinar->zoom_sdk_role ?? 'host',
        ]);

        if ($webinar->recording_status === null) {
            $webinar->recording_status = 'pending';
        }

        $webinar->save();

        return $webinar->fresh();
    }

    protected function webClientJoinUrl(?string $joinUrl, ?string $meetingId): ?string
    {
        if ($joinUrl === null || $joinUrl === '') {
            return $joinUrl;
        }

        $parsed = parse_url($joinUrl);

        if ($parsed === false) {
            return $joinUrl;
        }

        $path = $parsed['path'] ?? '';

        if ($path !== '' && Str::contains($path, '/wc/')) {
            return $joinUrl;
        }

        $meetingId = $meetingId ?: $this->extractMeetingIdFromPath($path);

        if ($meetingId === null || $meetingId === '') {
            return $joinUrl;
        }

        $host = $parsed['host'] ?? null;

        if ($host === null) {
            return $joinUrl;
        }

        $scheme = $parsed['scheme'] ?? 'https';
        $query = $parsed['query'] ?? '';
        $webClientPath = "/wc/{$meetingId}/join";

        $url = "{$scheme}://{$host}{$webClientPath}";

        if ($query !== '') {
            $url .= "?{$query}";
        }

        return $url;
    }

    protected function extractMeetingIdFromPath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (preg_match('#/(?:j|w)/(\d+)#', $path, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    public function endMeeting(LiveWebinar $webinar): void
    {
        if (! $webinar->zoom_meeting_id) {
            return;
        }

        $this->request()
            ->put($this->endpoint("/meetings/{$webinar->zoom_meeting_id}/status"), [
                'action' => 'end',
            ])
            ->throw();

        $webinar->update([
            'zoom_meeting_ended_at' => now(),
            'recording_status' => $webinar->recording_status ?? 'pending',
        ]);
    }

    public function retrieveMeeting(string $meetingId): array
    {
        $response = $this->request()->get($this->endpoint("/meetings/{$meetingId}"));

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json();
    }

    protected function createMeeting(LiveWebinar $webinar): array
    {
        $response = $this->request()->post($this->endpoint('/users/me/meetings'), $this->meetingPayload($webinar));

        if ($response->failed()) {
            Log::error('Unable to create Zoom meeting', [
                'webinar_id' => $webinar->id,
                'response' => $response->json(),
            ]);

            throw new RequestException($response);
        }

        return $response->json();
    }

    protected function updateMeeting(LiveWebinar $webinar): array
    {
        $response = $this->request()->patch(
            $this->endpoint("/meetings/{$webinar->zoom_meeting_id}"),
            $this->meetingPayload($webinar)
        );

        if ($response->failed()) {
            Log::error('Unable to update Zoom meeting', [
                'webinar_id' => $webinar->id,
                'meeting_id' => $webinar->zoom_meeting_id,
                'response' => $response->json(),
            ]);

            throw new RequestException($response);
        }

        $payload = $this->retrieveMeeting($webinar->zoom_meeting_id);

        return array_merge($payload, [
            'start_url' => Arr::get($payload, 'start_url'),
            'join_url' => Arr::get($payload, 'join_url'),
            'password' => Arr::get($payload, 'password'),
        ]);
    }

    public function listRecordings(LiveWebinar $webinar): array
    {
        $identifier = $this->meetingIdentifier($webinar);

        $response = $this->request()->get($this->endpoint("/meetings/{$identifier}/recordings"));

        if ($response->failed()) {
            Log::warning('Failed to fetch Zoom recordings', [
                'webinar_id' => $webinar->id,
                'meeting_id' => $webinar->zoom_meeting_id,
                'response' => $response->json(),
            ]);

            throw new RequestException($response);
        }

        return $response->json() ?? [];
    }

    public function downloadRecordingContent(string $downloadUrl): string
    {
        $token = $this->oauthClient->getAccessToken();

        $response = $this->http
            ->get($downloadUrl, ['access_token' => $token]);

        if ($response->failed()) {
            Log::error('Failed to download Zoom recording', [
                'url' => $downloadUrl,
                'status' => $response->status(),
            ]);

            throw new RequestException($response);
        }

        $body = $response->body();

        if ($body === '') {
            throw new RuntimeException('Zoom recording response body is empty.');
        }

        return $body;
    }

    /**
     * Download recording directly to a file using streaming to handle large files.
     */
    public function downloadRecordingToFile(string $downloadUrl, string $destinationPath): void
    {
        $token = $this->oauthClient->getAccessToken();
        $urlWithToken = $downloadUrl.(str_contains($downloadUrl, '?') ? '&' : '?').'access_token='.$token;

        $context = stream_context_create([
            'http' => [
                'timeout' => 3600, // 1 hour timeout for large files
                'follow_location' => true,
            ],
        ]);

        $source = @fopen($urlWithToken, 'rb', false, $context);

        if ($source === false) {
            throw new RuntimeException('Unable to open Zoom recording stream for download.');
        }

        $destination = @fopen($destinationPath, 'wb');

        if ($destination === false) {
            fclose($source);
            throw new RuntimeException('Unable to open destination file for writing.');
        }

        try {
            $bytesWritten = stream_copy_to_stream($source, $destination);

            if ($bytesWritten === false || $bytesWritten === 0) {
                throw new RuntimeException('Failed to download Zoom recording - no data received.');
            }

            Log::info('Zoom recording downloaded', [
                'url' => $downloadUrl,
                'bytes' => $bytesWritten,
                'destination' => $destinationPath,
            ]);
        } finally {
            fclose($source);
            fclose($destination);
        }
    }

    public function deleteRecordingFile(
        LiveWebinar $webinar,
        string $recordingId,
        bool $permanent = true
    ): void {
        if ($recordingId === '') {
            return;
        }

        $identifier = $this->meetingIdentifier($webinar);
        $query = $permanent ? ['action' => 'delete'] : [];

        $response = $this->request()->delete(
            $this->endpoint("/meetings/{$identifier}/recordings/{$recordingId}"),
            $query
        );

        if ($response->failed()) {
            Log::warning('Failed to delete Zoom recording file', [
                'live_webinar_id' => $webinar->id,
                'meeting_identifier' => $identifier,
                'recording_id' => $recordingId,
                'response' => $response->json(),
            ]);

            throw new RequestException($response);
        }
    }

    public function deleteAllRecordings(LiveWebinar $webinar, bool $permanent = true): void
    {
        $identifier = $this->meetingIdentifier($webinar);
        $query = $permanent ? ['action' => 'delete'] : [];

        $response = $this->request()->delete(
            $this->endpoint("/meetings/{$identifier}/recordings"),
            $query
        );

        if ($response->failed()) {
            Log::warning('Failed to delete Zoom recordings', [
                'live_webinar_id' => $webinar->id,
                'meeting_identifier' => $identifier,
                'response' => $response->json(),
            ]);

            throw new RequestException($response);
        }
    }

    protected function meetingPayload(LiveWebinar $webinar): array
    {
        $timezone = config('app.timezone', 'UTC');
        $startTime = $webinar->from->copy()->setTimezone($timezone)->toIso8601String();

        $payload = [
            'topic' => $webinar->topic,
            'type' => 2, // scheduled meeting
            'start_time' => $startTime,
            'duration' => (int) $webinar->duration,
            'agenda' => Str::limit((string) $webinar->description, 2000),
            'timezone' => $timezone,
            'settings' => [
                'host_video' => true,
                'participant_video' => false,
                'mute_upon_entry' => true,
                'approval_type' => 2,
                'registrants_email_notification' => false,
                'registrants_confirmation_email' => false,
                'auto_recording' => 'cloud',
                'waiting_room' => false,
                'allow_multiple_devices' => true,
                'meeting_authentication' => false,
                'join_before_host' => false,
                'watermark' => false,
                'audio' => 'both',
                'allow_host_control_participant_mute_state' => true,
                'disable_participant_video' => true,
            ],
        ];

        $templateId = config('services.zoom.live_webinar_template_id');

        if ($templateId !== null && $templateId !== '') {
            $payload['template_id'] = $templateId;
        }

        return $payload;
    }

    protected function request()
    {
        $token = $this->oauthClient->getAccessToken();

        return $this->http->withToken($token)->acceptJson();
    }

    protected function endpoint(string $path): string
    {
        return rtrim(config('services.zoom.base_url'), '/').$path;
    }

    protected function meetingIdentifier(LiveWebinar $webinar): string
    {
        if (! $webinar->zoom_meeting_id && ! $webinar->zoom_meeting_uuid) {
            throw new Exception('Zoom meeting identifier missing.');
        }

        return $webinar->zoom_meeting_uuid
            ? rawurlencode($webinar->zoom_meeting_uuid)
            : (string) $webinar->zoom_meeting_id;
    }
}
