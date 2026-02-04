<?php

namespace App\Console\Commands;

use App\Enums\DashboardDataType;
use App\Jobs\CreateTotalRiport;
use Illuminate\Console\Command;

class CreateTotalRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'riport:create-total-riport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create calculate case statistics and invoice sums for countries and contaract holders.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        CreateTotalRiport::dispatch(DashboardDataType::TYPE_COUNTRY_CASE_DATA->value);
        CreateTotalRiport::dispatch(DashboardDataType::TYPE_COUNTRY_INVOICE_DATA->value);
        CreateTotalRiport::dispatch(DashboardDataType::TYPE_CONTRACT_HOLDER_DATA->value);

        return Command::SUCCESS;
    }
}
