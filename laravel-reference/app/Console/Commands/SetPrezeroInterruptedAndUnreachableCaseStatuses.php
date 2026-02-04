<?php

namespace App\Console\Commands;

use App\Models\Riport;
use App\Models\RiportValue;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetPrezeroInterruptedAndUnreachableCaseStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'riports:set-prezero-interrupted-and-unreachable-case-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
        Set fake 10% interrupt_confirmed and 3% client_unreachable_confirmed 
        riport case status for the current quarter for Prezero Iberia at the end of the quarter
    ';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $case_status_values = RiportValue::query()
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereHas('riport', function ($q): void {
                $q->where('company_id', 1173);
                $q->where('from', '>=', Carbon::now()->subMonthNoOverflow()->startOfQuarter());
                $q->where('to', '<=', Carbon::now()->subMonthNoOverflow()->endOfQuarter());
            })
            ->get();

        $case_status_values->each(function ($riport_value) use ($case_status_values): void {
            if (! in_array($riport_value->value, ['interrupted', 'interrupted_confirmed', 'client_unreachable', 'client_unreachable_confirmed'])) {

                $total_count = $case_status_values->count();
                $interrupted_count = $case_status_values->whereIn('value', ['interrupted', 'interrupted_confirmed'])->count();
                $unreachable_count = $case_status_values->whereIn('value', ['client_unreachable', 'client_unreachable_confirmed'])->count();

                // IF the number of total interrupted cases is less than 10% set the current riport value to interrupted_confirmed
                if ($interrupted_count === 0 || (ceil($interrupted_count / $total_count * 100) < 10)) {
                    $riport_value->update([
                        'value' => 'interrupted_confirmed',
                    ]);

                    return;
                }

                // IF the number of total unreachable cases is less than 3% set the current riport value to client_unreachable_confirmed
                if ($unreachable_count === 0 || (ceil($unreachable_count / $total_count * 100) < 3)) {
                    $riport_value->update([
                        'value' => 'client_unreachable_confirmed',
                    ]);

                    return;
                }
            }
        });

    }
}
