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

class Send3MonthsCaseEmails implements ShouldQueue
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
        Log::info('[JOB][Send3MonthsCaseEmails] fired');

        $deadline = Carbon::now()->subDays(60)->endOfDay();

        $cases = Cases::query()
            ->whereHas('firstConsultation', function ($query) use ($deadline): void {
                $query->where('consultations.created_at', '<=', $deadline);
            })
            ->with('experts')
            ->where('cases.email_sent_3months', 0)
            ->whereIn('status', ['opened', 'assigned_to_expert', 'employee_contacted'])
            ->orderBy('id', 'desc')
            ->get();

        Log::info('[JOB][Send3MonthsCaseEmails] cases: '.$cases->pluck('id'));

        foreach ($cases as $case) {
            Send3MonthsCaseEmail::dispatch($case);
            $case->email_sent_3months = 1;
            $case->save();
        }
    }
}
