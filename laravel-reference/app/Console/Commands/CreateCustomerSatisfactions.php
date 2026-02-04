<?php

namespace App\Console\Commands;

use App\Jobs\CreateCustomerSatisfactionForCompany;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCustomerSatisfactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer-satisfactions:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create customer satisfactions in the first day of every month.';

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
        Log::info('[COMMAND][CreateCustomerSatisfactions]: fired!');

        $from = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $to = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');

        Company::query()->get()->map(function ($company) use ($from, $to): void {
            CreateCustomerSatisfactionForCompany::dispatch($company, $from, $to);
        });

        return self::SUCCESS;
    }
}
