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

class Send24HoursCaseEmails implements ShouldQueue
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
        Log::info('[JOB][Send24HoursCaseEmails] fired');

        $cases = Cases::query()
            ->doesntHave('consultations')
            ->whereDate('created_at', '<', Carbon::parse('-24 hours'))
            ->with('experts')
            ->where('email_sent_24hours', 0)
            ->whereIn('status', ['opened', 'assigned_to_expert'])
            ->orderBy('id', 'desc')
            ->get();

        Log::info('[JOB][Send24HoursCaseEmails] cases: '.$cases->pluck('id'));

        foreach ($cases as $case) {
            Send24HoursCaseEmail::dispatch($case);
            $case->email_sent_24hours = 1;
            $case->save();
        }
    }
}
