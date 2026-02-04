<?php

namespace App\Jobs;

use App\Models\Cases;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Send5DaysCaseEmails implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
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
        Log::info('[JOB][Send5DaysCaseEmails] fired');

        $cases = Cases::query()
            ->doesntHave('consultations')
            ->whereDate('created_at', '<', Carbon::parse('-5 days'))
            ->with('experts')
            ->where('email_sent_5days', 0)
            ->whereIn('status', ['employee_contacted'])
            ->orderBy('id', 'desc')
            ->get();

        Log::info('[JOB][Send5DaysCaseEmails] cases: '.$cases->pluck('id'));

        foreach ($cases as $case) {
            Send5DaysCaseEmail::dispatch($case);
            $case->email_sent_5days = 1;
            $case->save();
        }
    }
}
