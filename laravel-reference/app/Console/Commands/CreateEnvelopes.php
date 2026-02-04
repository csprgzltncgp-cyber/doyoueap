<?php

namespace App\Console\Commands;

use App\Models\DirectInvoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateEnvelopes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:envelopes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Envelopes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][CreateEnvelopes]: fired!');

        $direct_invoices = DirectInvoice::query()
            ->whereDate('to', Carbon::now()->subMonthNoOverflow()->endOfMonth())
            ->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
            ->get()->filter(fn ($direct_invoice) => $direct_invoice->data['billing_data']['send_invoice_by_post']);

        foreach ($direct_invoices as $direct_invoice) {
            $direct_invoice->envelope()->firstOrCreate([
                'printed_at' => null,
            ]);
        }

        return Command::SUCCESS;
    }
}
