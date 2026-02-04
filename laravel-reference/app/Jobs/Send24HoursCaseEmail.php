<?php

namespace App\Jobs;

use App\Mail\Case24Hours;
use App\Models\Cases;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Send24HoursCaseEmail implements ShouldQueue
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
    public function __construct(protected Cases $case) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->case->case_accepted_expert();

        if ($user !== null) {
            Mail::to($user->email)->send(new Case24Hours($this->case, $user));
            Log::info('[JOB][Send24HoursCaseEmail] case: '.$this->case->id.' sent.');
        }

    }
}
