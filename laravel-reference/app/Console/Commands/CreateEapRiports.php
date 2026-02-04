<?php

namespace App\Console\Commands;

use App\Jobs\CreateEapRiportForCompany;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CreateEapRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eap-riports:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create eap riports in the first day every quarter.';

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
        Log::info('[COMMAND][CreateEapRiports]: fired!');

        $from = Carbon::now()->subDay()->startOfQuarter()->format('Y-m-d');
        $to = Carbon::now()->subDay()->endOfQuarter()->format('Y-m-d');

        Company::query()
            ->whereHas('org_datas', function ($query): void {
                $query->where('contract_holder_id', 2);
            })
            ->get()
            ->map(function ($company) use ($from, $to): void {
                CreateEapRiportForCompany::dispatch($company, $from, $to);
            });

        return self::SUCCESS;
    }
}
