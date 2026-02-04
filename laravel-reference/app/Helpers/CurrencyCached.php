<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyCached
{
    /**
     * @var array currency rates
     */
    protected array $rates;

    /**
     * @var string cache key
     */
    protected string $cacheKey = 'currency-rates';

    public function __construct(?int $cacheTtl = null)
    {
        $ttl = $cacheTtl ?? 24 * 60 * 60;

        $this->rates = Cache::remember($this->cacheKey, $ttl, function () {
            $response = Http::get('http://api.exchangerate.host/historical', [
                'access_key' => config('services.exchangertates.api'),
                'date' => Carbon::now()->format('Y-m-d'),
                'currencies' => 'CHF, EUR, USD, HUF, CZK, PLN, RON, RSD',
            ]);

            if ($response->failed() || ! $response->json()['success']) {
                return [
                    'CHF' => 0.91242,
                    'EUR' => 0.948325,
                    'HUF' => 367.204991,
                    'CZK' => 23.1746,
                    'PLN' => 4.370018,
                    'RON' => 4.718202,
                    'USD' => 1.0,
                    'RSD' => 108.05,
                ];
            }

            return collect($response->json()['quotes'])->mapWithKeys(fn ($item, $key): array => [substr($key, 3) => $item])->merge(['USD' => 1.0])->toArray();
        });
    }

    /**
     * Currency conversion.
     */
    public function convert(float $amount, string $toCurrency = 'CHF', string $fromCurrency = 'EUR'): float
    {
        return round($amount * $this->rates[$toCurrency] / $this->rates[$fromCurrency], 2);
    }
}
