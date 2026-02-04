<?php

namespace App\Jobs;

use App\Mail\LowCustomerSatisfactionEmail;
use App\Models\Cases;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLowCustomerSatisfactionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cases::query()
            ->whereNotNull(['customer_satisfaction'])
            ->whereBetween('confirmed_at', [Carbon::now()->subDay()->startOfDay(), Carbon::now()->subDay()->endOfDay()])
            ->where('customer_satisfaction', '<', 4)
            ->get()->each(function (Cases $case): void {
                Mail::to('maria.szabo@cgpeu.com')->send(new LowCustomerSatisfactionEmail($case));
            });
    }
}
