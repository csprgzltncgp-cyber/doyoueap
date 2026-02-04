<?php

namespace App\Jobs;

use App\Traits\EapOnline\Riport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateEapRiportForCompany implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use Riport;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $company, public $from, public $to) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $riport = $this->company->eap_riports()->create([
            'from' => $this->from,
            'to' => $this->to,
            'is_active' => false,
        ]);

        $this->generate_riport($this->company, $riport, $this->from, $this->to);

        Log::info('[JOB][CreateEapRiportForCompany] New eap riport created for: '.$this->company->name);
    }
}
