<?php

namespace App\Console\Commands;

use App\Models\Cases;
use App\Models\CaseValues;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandBase;

class DeleteSensitiveDataAfter3Months extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:delete-sensitive-data-after-3-months';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all sensitive data in ongoing cases after 3 months';

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
        /*
         * Sensitive data to delete:
         *
         * Name -> 4
         * Phone -> 17
         * Email -> 18
         * Comment1 -> 19
         * Comment2 -> 36
         * Chat/Email message -> 20,
         * Covid 19 -> 46
         *
         * Sensitive data to hide:
         * City -> 5
         * Gender -> 10
         * Company -> 2
         * Language -> 32
         * Age -> 11
         */

        Log::info('[COMMAND][DeleteSensitiveDataAfter3Months]: fired!');
        $cases_to_update = Cases::query()
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)->where('value', '<', Carbon::now()->subMonths(3));
            })->whereIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->get();

        if ($cases_to_update->count() > 0) {
            Log::info('[COMMAND][DeleteSensitiveDataAfter3Months]: found '.$cases_to_update->count().' cases to update!');

            foreach ($cases_to_update as $case) {
                $case->values()->each(function (CaseValues $value): void {
                    if (in_array($value->case_input_id, [4, 17, 18, 19, 36, 20, 46])) {
                        $value->value = 'XXX';
                        $value->save();
                    }
                });
            }
        }

        return CommandBase::SUCCESS;
    }
}
