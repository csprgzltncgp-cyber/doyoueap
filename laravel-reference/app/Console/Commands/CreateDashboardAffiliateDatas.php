<?php

namespace App\Console\Commands;

use App\Jobs\DashboardData\CreateAffiliateDataForCompany;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateDashboardAffiliateDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:dashboard-affiliate-datas {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create dashboard case datas in the first day of every month.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][CreateDashboardAffiliateDatas]: fired!');

        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now()->subMonthWithNoOverflow();

        $from = $date->copy()->startOfMonth();
        $to = $date->copy()->endOfMonth();

        Company::query()->get()->map(function ($company) use ($from, $to): void {
            CreateAffiliateDataForCompany::dispatch($company, $from, $to);
        });

        return Command::SUCCESS;
    }
}
