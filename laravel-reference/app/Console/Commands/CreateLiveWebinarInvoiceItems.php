<?php

namespace App\Console\Commands;

use App\Models\InvoiceLiveWebinarData;
use App\Models\LiveWebinar;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateLiveWebinarInvoiceItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:live-webinar-invoice-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Live webinar invoice datas(invoice items) for experts';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $from = Carbon::now()->subMonthWithoutOverflow()->startOfMonth();
        $to = Carbon::now()->subMonthWithoutOverflow()->endOfMonth();

        LiveWebinar::query()
            ->where('price', '>', 0)
            ->whereBetween('to', [$from, $to])
            ->whereDoesntHave('invoice_live_webinar_data')
            ->each(function (LiveWebinar $live_webinar): void {
                InvoiceLiveWebinarData::query()->create([
                    'live_webinar_id' => $live_webinar->id,
                    'activity_id' => $live_webinar->activity_id,
                    'expert_id' => $live_webinar->expert->id,
                    'price' => $live_webinar->price,
                    'currency' => $live_webinar->currency,
                ]);
            });
    }
}
