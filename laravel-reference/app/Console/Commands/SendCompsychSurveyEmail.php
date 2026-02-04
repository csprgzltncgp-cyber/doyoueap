<?php

namespace App\Console\Commands;

use App\Jobs\SendCompsychSurveyEmailJob;
use App\Models\Cases;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class SendCompsychSurveyEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-compsych-survey-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the survey to clients for any closed Compsych psychological cases older than 3 month.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[SendCompsychSurveyEmail] Fired!');

        Cases::query()
            ->where('status', ['confirmed', 'client_unreachable', 'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed'])
            ->where('created_at', '>=', Carbon::parse('2025-10-22 00:01:00'))
            ->where('confirmed_at', '<=', Carbon::now()->subDays(90)->startOfDay())
            ->where('email_sent_compsych_survey_3month', '!=', 1)
            ->whereRelation('case_type', 'value', 1) // Psychological case only
            ->whereHas('company', function (Builder $query): void {
                $query->whereHas('org_datas', fn (Builder $query) => $query->where('contract_holder_id', 3)); // 3 - Compsych
            })
            ->get()->each(function (Cases $case): void {
                SendCompsychSurveyEmailJob::dispatch($case);
            });
    }
}
