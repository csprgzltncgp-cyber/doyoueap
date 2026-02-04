<?php

namespace App\Console\Commands;

use App\Jobs\VolumeRequest\CreateVolumeRequestsJob;
use Illuminate\Console\Command;

class CreateAndSetVolumeRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-and-set-volume-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the volume request for all changing invoice items for the current month';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CreateVolumeRequestsJob::dispatch();
    }
}
