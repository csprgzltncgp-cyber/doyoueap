<?php

namespace App\Console\Commands;

use App\Jobs\CustomCompanyExports\Colep;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateCustomCompanyRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'riports:create-custom-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create create riports for custom company and save in excel format.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $from = Carbon::now()->subMonthWithNoOverflow()->startOfMonth();
        $to = Carbon::now()->subMonthWithNoOverflow()->endOfMonth();

        Colep::dispatch($from, $to);

        return self::SUCCESS;
    }
}
