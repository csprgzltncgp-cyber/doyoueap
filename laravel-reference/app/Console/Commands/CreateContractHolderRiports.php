<?php

namespace App\Console\Commands;

use App\Jobs\ContractHolderExports\Compsych;
use App\Jobs\ContractHolderExports\LifeWorks;
use App\Jobs\ContractHolderExports\Optum;
use App\Jobs\ContractHolderExports\Pulso;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateContractHolderRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'riports:create-contract-holder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create create riports for contract holders and save in excel format.';

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

        Compsych::dispatch($from, $to);
        Pulso::dispatch($from, $to);
        LifeWorks::dispatch($from, $to);
        Optum::dispatch($from, $to);

        return self::SUCCESS;
    }
}
