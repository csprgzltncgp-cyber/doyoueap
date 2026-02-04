<?php

namespace App\Console\Commands;

use App\Jobs\VolumeRequest\CreateVolumeRequestsJob;
use Illuminate\Console\Command;

class CreateVolumeRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-volume-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CreateVolumeRequestsJob::dispatch();
    }
}
