<?php

namespace App\Console\Commands;

use App\Enums\DashboardDataType;
use App\Imports\LifeworksDataImport;
use App\Models\DashboardData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CreateDashboardLifeworksDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:dashboard-lifeworks-datas {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create dashboard lifeworks datas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][CreateDashboardLifeworksDatas]: fired!');

        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now()->subMonthWithNoOverflow();

        DashboardData::query()
            ->where('type', DashboardDataType::TYPE_LIFEWORKS_DATA)
            ->where('data->from', '>=', $date->startOfMonth()->format('Y-m-d'))
            ->where('data->to', '<=', $date->endOfMonth()->format('Y-m-d'))
            ->delete();

        $file_path = storage_path('app/dashboard-data/lifeworks-data-'.$date->format('Y-m').'.xlsx');

        if (! file_exists($file_path)) {
            return Command::FAILURE;
        }

        Excel::import(new LifeworksDataImport($date), $file_path);

        return Command::SUCCESS;
    }
}
