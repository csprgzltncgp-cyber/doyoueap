<?php

namespace App\Console\Commands;

use App\Models\Cases;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandBase;

class DeleteCasesAfter3Months extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:delete-after-3-months';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete cases after 3 months.';

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
        Log::info('[COMMAND][DeleteCasesAfter3Months]: fired!');
        $not_confirmed_cases = Cases::query()
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)->where('value', '<', Carbon::now()->subMonths(3));
            })
            ->whereIn('status', ['interrupted', 'interrupted_confirmed', 'client_unreachable', 'client_unreachable_confirmed'])
            ->get();

        if ($not_confirmed_cases->count() > 0) {
            foreach ($not_confirmed_cases as $case) {
                $case->forceDelete();
            }
            Log::info('[COMMAND][DeleteCasesAfter3Months]: Deleted not confirmed cases with id(s): '.implode(', ', $not_confirmed_cases->pluck('id')->toArray()));
        }

        $confirmed_cases = Cases::query()
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)->where('value', '<', Carbon::now()->subMonths(3));
            })
            ->whereIn('status', ['confirmed'])
            ->get();

        if ($confirmed_cases->count() > 0) {
            foreach ($confirmed_cases as $case) {
                $case->forceDelete();
            }
            Log::info('[COMMAND][DeleteCasesAfter3Months]: Deleted confirmed cases with id(s): '.implode(', ', $confirmed_cases->pluck('id')->toArray()));
        }

        return CommandBase::SUCCESS;
    }
}
