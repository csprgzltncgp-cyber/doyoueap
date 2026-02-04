<?php

namespace App\Console\Commands;

use App\Models\ExpertCurrencyChange;
use Illuminate\Console\Command;

class StoreExpertCurrencyChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:expert-currency-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expert invoice data based on currency change data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        ExpertCurrencyChange::query()->with(['expert', 'expert.invoice_datas'])->get()->each(function ($currency_change): void {
            if (! empty($currency_change->hourly_rate_30_currency) && ! empty($currency_change->hourly_rate_30)) {
                $currency_change->expert->invoice_datas->update([
                    'hourly_rate_30' => $currency_change->hourly_rate_30,
                    'currency' => $currency_change->hourly_rate_30_currency,
                ]);
            }
            if (empty($currency_change->hourly_rate_50_currency)) {
                return;
            }
            if (empty($currency_change->hourly_rate_50)) {
                return;
            }
            $currency_change->expert->invoice_datas->update([
                'hourly_rate_50' => $currency_change->hourly_rate_50,
                'currency' => $currency_change->hourly_rate_50_currency,
            ]);
        });

        return Command::SUCCESS;
    }
}
