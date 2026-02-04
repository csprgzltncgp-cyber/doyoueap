<?php

namespace App\Console\Commands;

use App\Models\CustomerSatisfaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ActivateCustomerSatisfactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer-satisfactions:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate customer satisfactions on every quarter.';

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

        CustomerSatisfaction::query()
            ->where([
                'is_active' => false,
            ])
            ->where('from', '>=', $from)
            ->where('to', '<=', $to)
            ->get()->map(function ($customer_satisfaction): void {
                $customer_satisfaction->is_active = true;
                $customer_satisfaction->save();
            });

        return self::SUCCESS;
    }
}
