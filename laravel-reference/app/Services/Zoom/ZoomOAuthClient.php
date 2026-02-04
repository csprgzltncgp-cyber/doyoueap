<?php

namespace App\Services\Zoom;

use Exception;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class ZoomOAuthClient
{
    public function __construct(
        protected HttpFactory $http,
        protected CacheRepository $cache
    ) {}

    public function getAccessToken(): string
    {
        $token = $this->cache->get('zoom.oauth_token');

        if ($token) {
            return $token;
        }

        return $this->requestAccessToken();
    }

    protected function requestAccessToken(): string
    {
        $clientId = config('services.zoom.client_id');
        $clientSecret = config('services.zoom.client_secret');
        $accountId = config('services.zoom.account_id');

        $response = $this->http
            ->withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->failed()) {
            $exception = new RequestException($response);
            Log::error('Zoom OAuth token request failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw $exception;
        }

        $payload = $response->json();

        if (! isset($payload['access_token'])) {
            Log::error('Zoom OAuth response missing access_token', ['response' => $payload]);
            throw new Exception('Zoom access token missing from response.');
        }

        $expiresIn = (int) ($payload['expires_in'] ?? 3600);
        $ttl = max($expiresIn - 60, 60);
        $this->cache->put('zoom.oauth_token', $payload['access_token'], now()->addSeconds($ttl));

        return $payload['access_token'];
    }
}
