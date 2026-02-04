<?php

namespace App\Console\Commands;

use App\Exports\TwentyFourHourAssignedCasesExport;
use App\Mail\Send24HourAssignedCasesEmail;
use App\Models\Cases;
use App\Models\OrgData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class Send24HourAssignedCases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-24hour-assigned-cases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email with an attached excel to Tompa Anita about the cases that were assigned to experts in the last 24 hour.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][Send24HourAssignedCases]: fired!');

        $today = Carbon::now();

        $from = $today->copy()->startOfWeek();
        $to = $today->copy()->endOfDay();

        $cases = Cases::query()
            ->whereHas('experts', fn ($q) => $q->whereBetween('expert_x_case.created_at', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')]))
            ->get()
            ->filter(fn ($case): bool =>
                // Check if the date the case was assigned for the first time is the same day as the $pervious_day
                $case->first_assigned_expert_by_date() &&
                $case->first_assigned_expert_by_date()->getRelationValue('pivot')->created_at->isBetween($from, $to))->sortBy(fn ($case) =>
                // Sort cases by when they were first assigned to an expert
                optional($case->first_assigned_expert_by_date())->pivot->created_at);

        if ($cases->isNotEmpty()) {
            // Seperate WPO cases into new collection
            $wpo_cases = new Collection;
            $cases->each(function (Cases $case, int $key) use (&$wpo_cases, &$cases): void {
                $org_data = OrgData::query()->where(['company_id' => $case->company_id, 'country_id' => $case->country_id])->first();

                if ($org_data && $org_data->contract_holder_id === 6) { // 6 - WPO/Telus
                    $wpo_cases->push($case);
                    $cases->forget($key); // Remove WPO case from the original collection
                }
            });

            $file_path = null;
            if ($cases->isNotEmpty()) {
                Excel::store(new TwentyFourHourAssignedCasesExport($cases), $from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx', 'private');
                $file_path = storage_path('app/'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx');
            }

            $file_path_wpo = null;
            if ($wpo_cases->isNotEmpty()) {
                Excel::store(new TwentyFourHourAssignedCasesExport($wpo_cases), $from->format('Y-m-d').'-'.$to->format('Y-m-d').'_WPO.xlsx', 'private');
                $file_path_wpo = storage_path('app/'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'_WPO.xlsx');
            }

            Mail::to('anita.tompa@cgpeu.com')->send(new Send24HourAssignedCasesEmail($file_path, $file_path_wpo, $from, $to));

            // Remove temp excel file
            if (File::exists($file_path)) {
                File::delete($file_path);
            }
        }
    }
}
