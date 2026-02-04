<?php

namespace App\Jobs;

use App\Models\WorkshopCase;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Send48HoursWorkshopReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Log::info('[JOB][Send48HoursWorkshopReminderEmails] fired');

        $workshop_cases = WorkshopCase::query()
            ->with('user')
            ->whereDate('date', Carbon::parse()->addDays(2)->format('Y-m-d'))
            ->whereNotNull('expert_id')
            ->get();

        Log::info('[JOB][Send48HoursWorkshopReminderEmails] workshops: '.$workshop_cases->pluck('activity_id'));

        foreach ($workshop_cases as $workshop_case) {
            Send48HoursWorkshopReminderEmail::dispatch($workshop_case);
        }
    }
}
