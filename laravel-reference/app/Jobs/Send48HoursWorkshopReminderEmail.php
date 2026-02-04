<?php

namespace App\Jobs;

use App\Mail\Workshop48Hours;
use App\Models\WorkshopCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Send48HoursWorkshopReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected WorkshopCase $workshop_case)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->workshop_case->user->email !== '' && $this->workshop_case->user->email !== '0') {
            Mail::to($this->workshop_case->user->email)->send(new Workshop48Hours($this->workshop_case));

            Log::info('[JOB][Send48HoursWorkshopReminderEmail] workshop: '.$this->workshop_case->activity_id.' sent.');
        }
    }
}
