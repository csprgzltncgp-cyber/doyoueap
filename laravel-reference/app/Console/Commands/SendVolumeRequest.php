<?php

namespace App\Console\Commands;

use App\Jobs\VolumeRequest\SendVolumeRequestsJob;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendVolumeRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-volume-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out volume request email to companies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][SendVolumeRequest]: fired!');

        Company::query()
            ->where('active', 1)
            ->whereHas('invoice_items', function ($query): void {
                $query->whereHas('volume', function ($query): void {
                    $query->where('is_changing', 1);
                });
            })
            ->get()->reduce(function (?int $seconds, Company $company): int {
                SendVolumeRequestsJob::dispatch($company)->delay($seconds);

                return $seconds + 30;
            });

        return Command::SUCCESS;
    }
}
