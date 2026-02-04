<?php

namespace App\Jobs\VolumeRequest;

use App\Models\DirectBillingData;
use App\Models\Volume;
use App\Models\VolumeRequest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateVolumeRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * This job runs at the start each month and creates VolumeRequest for companies for the current month.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][CreateVolumeRequestsJob]: fired!');

        Volume::query()
            ->where('is_changing', true)
            ->whereHas('invoice_item', function ($q): void {
                $q->whereHas('company', fn ($q) => $q->where('active', 1));
            })
            ->get()->each(function ($volume): void {

                // Check billing frequency
                $direct_invoice_data = $volume->invoice_item->direct_invoice_data;

                if (empty($direct_invoice_data->direct_billing_data)) {
                    return;
                }

                if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_QUARTELY && Carbon::now()->subMonthNoOverflow()->month % 3 !== 0) {
                    return;
                }

                if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_YEARLY && Carbon::now()->subMonthNoOverflow()->month !== 12) {
                    return;
                }

                if ($direct_invoice_data->company->country_differentiates->invoicing && ! $volume->invoice_item->country_id) {
                    return;
                }

                VolumeRequest::query()->create([
                    'volume_id' => $volume->id,
                    'date' => now()->subMonth()->startOfMonth(),
                ]);
            });

        // Remove any requests from the previous month that no longer has volume
        VolumeRequest::query()
            ->with('volume')
            ->where('date', Carbon::now()->subMonthsWithNoOverflow(2)->startOfMonth())
            ->get()->each(function ($volume_request): void {
                if (empty($volume_request->volume) || empty($volume_request->volume->invoice_item)) {
                    $volume_request->delete();
                }
            });
    }
}
