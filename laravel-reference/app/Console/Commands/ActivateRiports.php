<?php

namespace App\Console\Commands;

use App\Models\Riport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActivateRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'riports:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate riports on every quarter.';

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
        $quarter = Carbon::now()->startOfQuarter()->subDay()->quarter;

        // check if today is in the first quarter of the current year
        if (Carbon::now()->quarter == 1) {
            $from = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3);
        } else {
            $from = Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->startOfYear()->addMonths($quarter * 3);
        }

        Riport::query()
            ->where([
                'is_active' => false,
            ])
            ->where('from', '>=', $from)
            ->where('to', '<=', $to)
            ->get()->map(function ($riport): void {
                $riport->is_active = true;
                $riport->save();
            });

        return self::SUCCESS;
    }
}
