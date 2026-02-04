<?php

namespace Database\Seeders;

use App\Enums\VolumeRequestStatusEnum;
use App\Models\DirectInvoice;
use App\Models\VolumeRequest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SetDirectInvoiceHeadCountsToNull extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set direct invoice headcounts from volume requests
        $volume_requests = VolumeRequest::query()
            ->whereIn('status', [VolumeRequestStatusEnum::COMPLETED, VolumeRequestStatusEnum::AUTO_COMPLETED])
            ->where('date', Carbon::now()->subMonthNoOverflow()->startOfMonth())
            ->get();

        $volume_requests->each(function ($volume_request): void {
            $invoice_item = optional($volume_request->volume)->invoice_item;
            $company = ($invoice_item) ? $volume_request->volume->invoice_item->company : null;
            if ($company) {
                $company->direct_invoices()
                    ->where('from', Carbon::now()->subMonthWithNoOverflow()->startOfMonth()->format('Y-m-d'))
                    ->where('to', Carbon::now()->subMonthWithNoOverflow()->endOfMonth()->format('Y-m-d'))
                    ->whereJsonContains('data->invoice_items', ['id' => $invoice_item->id]) // Find the direct invoice that contains the invoice item by id
                    ->each(function (DirectInvoice $direct_invoice) use ($invoice_item): void {
                        $data = $direct_invoice->data;
                        $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($invoice_item): array {
                            if ($item['id'] == $invoice_item->id) {
                                $item['volume']['value'] = null;
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
