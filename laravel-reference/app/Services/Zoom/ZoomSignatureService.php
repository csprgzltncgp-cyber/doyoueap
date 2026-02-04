<?php

namespace App\Services\Zoom;

use App\Models\LiveWebinar;
use Exception;

class ZoomSignatureService
{
    public function createForHost(LiveWebinar $webinar, int $role = 1, int $durationMinutes = 120): array
    {
        $meetingNumber = $webinar->zoom_meeting_id;

        if (! $meetingNumber) {
            throw new Exception('Cannot generate signature for webinar without a Zoom meeting id.');
        }

        $sdkKey = config('services.zoom.sdk_key');
        $sdkSecret = config('services.zoom.sdk_secret');

        $issuedAt = now()->timestamp - 30;
        $expireAt = $issuedAt + ($durationMinutes * 60);

        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = $this->base64UrlEncode(json_encode([
            // 'appKey' => $sdkKey,
            'sdkKey' => $sdkKey,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $issuedAt,
            'exp' => $expireAt,
            'tokenExp' => $expireAt,
        ]));

        $signature = $this->base64UrlEncode(hash_hmac('sha256', sprintf('%s.%s', $header, $payload), $sdkSecret, true));

        return [
            'signature' => sprintf('%s.%s.%s', $header, $payload, $signature),
            'expires_at' => now()->addSeconds($durationMinutes * 60),
        ];
    }

    protected function base64UrlEncode(string $input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
