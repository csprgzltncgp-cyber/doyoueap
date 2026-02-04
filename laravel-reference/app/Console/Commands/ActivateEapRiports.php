<?php

namespace App\Console\Commands;

use App\Models\EapOnline\EapRiport;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ActivateEapRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eap-riports:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate eap riports on every quarter.';

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
        $from = Carbon::now()->subDay()->startOfQuarter()->format('Y-m-d');
        $to = Carbon::now()->subDay()->endOfQuarter()->format('Y-m-d');

        EapRiport::query()
            ->where('is_active', false)
            ->where('from', '>=', $from)
            ->where('to', '<=', $to)
            ->get()->map(function ($riport): void {
                $riport->is_active = true;
                $riport->save();
            });

        return self::SUCCESS;
    }
}
