<?php

namespace App\Console\Commands;

use App\Enums\VolumeRequestStatusEnum;
use App\Models\DirectInvoice;
use App\Models\VolumeRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetDirectInvoiceHeadcountFromVolumeRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-direct-invoice-headcount-from-volume-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set direct invoice changing volume headcounts from available volume request filled out by the companies';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][SetDirectInvoiceHeadcountFromVolumeRequest]: fired!');

        // Set the headcount of the pending volume requests form the previous month to the value of the month before the previous month.
        VolumeRequest::query()
            ->where('status', VolumeRequestStatusEnum::PENDING)
            ->where('date', Carbon::now()->subMonthNoOverflow()->startOfMonth())
            ->get()->each(function (VolumeRequest $request): void {
                $previous_headcount = VolumeRequest::query() // Get the last record where the headcount is available.
                    ->where('volume_id', $request->volume_id)
                    ->orderBy('id', 'desc')
                    ->whereNotNull('headcount')
                    ->first();

                if ($previous_headcount) {
                    $request->update([
                        'headcount' => $previous_headcount->headcount,
                        'status' => VolumeRequestStatusEnum::AUTO_COMPLETED,
                    ]);
                }
            });

        // Set direct invoice headcounts from volume requests
        $volume_requests = VolumeRequest::query()
            ->whereIn('status', [VolumeRequestStatusEnum::COMPLETED, VolumeRequestStatusEnum::AUTO_COMPLETED])
            ->where('date', Carbon::now()->subMonthNoOverflow()->startOfMonth())
            ->get();

        $volume_requests->each(function ($volume_request): void {
            $headcount = $volume_request->headcount;
            $invoice_item = optional($volume_request->volume)->invoice_item;
            $company = ($invoice_item) ? $volume_request->volume->invoice_item->company : null;

            if ($company) {
                $company->direct_invoices()
                    ->where('from', Carbon::now()->subMonthWithNoOverflow()->startOfMonth()->format('Y-m-d'))
                    ->where('to', Carbon::now()->subMonthWithNoOverflow()->endOfMonth()->format('Y-m-d'))
                    ->whereJsonContains('data->invoice_items', ['id' => $invoice_item->id]) // Find the direct invoice that contains the invoice item by id
                    ->each(function (DirectInvoice $direct_invoice) use ($invoice_item, $headcount): void {
                        $data = $direct_invoice->data;
                        $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($invoice_item, $headcount): array {
                            if ($item['id'] == $invoice_item->id) {
                                $item['volume']['value'] = $headcount;
                            }

                            return $item;
                        })->toArray();

                        $direct_invoice->update([
                            'data' => $data,
                        ]);
                    });
            }
        });
    }
}
