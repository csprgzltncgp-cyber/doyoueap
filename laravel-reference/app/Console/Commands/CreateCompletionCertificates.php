<?php

namespace App\Console\Commands;

use App\Models\DirectInvoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCompletionCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:completion-certificates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Completion Certificates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][CreateCompletionCertificates]: fired!');

        $direct_invoices = DirectInvoice::query()
            ->whereDate('to', Carbon::now()->subMonthNoOverflow()->endOfMonth())
            ->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
            ->get()->filter(fn ($direct_invoice): bool => $direct_invoice->data['billing_data']['send_completion_certificate_by_post'] || $direct_invoice->data['billing_data']['send_completion_certificate_by_email']);

        foreach ($direct_invoices as $direct_invoice) {
            $direct_invoice->completion_certificate()->firstOrCreate([
                'with_header' => false,
                'path' => null,
                'printed_at' => null,
                'sent_at' => null,
            ]);
        }

        return self::SUCCESS;
    }
}
