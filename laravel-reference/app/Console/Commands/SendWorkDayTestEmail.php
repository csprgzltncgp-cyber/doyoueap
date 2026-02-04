<?php

namespace App\Console\Commands;

use App\Mail\WorkDayTestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWorkDayTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-work-day-test-email {day?}';

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
        $day = $this->argument('day');
        Mail::to('gergo.janosdeak@cgpeu.com')->send(new WorkDayTestEmail((int) $day));
    }
}
