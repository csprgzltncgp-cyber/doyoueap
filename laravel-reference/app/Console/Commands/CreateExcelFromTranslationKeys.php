<?php

namespace App\Console\Commands;

use App\Exports\FileTranslationsExport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class CreateExcelFromTranslationKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-excel-from-translation-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $keys = [
            'common.customer_satisfaction_text',
            'common.save',
            'common.green_text',
            'riport.health_map_desc',
            'riport.welcome.page2.psychology',
            'riport.welcome.page3.finance',
            'riport.law',
            'common.health-coaching',
            'riport.under_19',
            'riport.from_20_to_29',
            'riport.from_30_to_39',
            'riport.from_40_to_49',
            'riport.from_50_to_59',
            'riport.above_60',
            'common.username',
            'common.password',
            'common.login',
            'common.client-maintenance',
            'common.password-again',
            'prizegame.lottery.guess_count',
            'riport.prize_game_desc',
            'prizegame.lottery.prize_start',
            'prizegame.lottery.prize_stop',
            'common.winner',
            'prizegame.lottery.username',
            'prizegame.lottery.email',
            'prizegame.lottery.excel_download',
            'program-usage.usage',
            'program-usage.usage_global',
            'program-usage.buble_title',
            'program-usage.buble_number_percentage',
            'program-usage.buble_number_multiply',
            'program-usage.best_usage_month',
            'program-usage.problem_type',
            'program-usage.gender',
            'program-usage.age',
            'eap-online.riports.login_statistics_front',
            'riport.total',
            'riport.1_quarter',
            'riport.2_quarter',
            'riport.3_quarter',
            'riport.4_quarter',
            'riport.cumulated_numbers',
            'riport.closed_cases',
            'riport.closed_cases_info',
            'riport.interrupted_cases',
            'riport.interrupted_cases_info',
            'riport.client_unreachable_cases',
            'riport.client_unreachable_cases_info',
            'riport.consultations',
            'riport.in_progress_cases_short',
            'riport.in_progress_cases_info',
            'riport.consultations_ongoing',
            'riport.workshop_participants',
            'riport.crisis_participants',
            'riport.orientation_participants',
            'riport.health_day_participants',
            'riport.expert_outplacement_participants',
            'riport.prizegame_participants',
            'riport.no_available_data',
            'riport.in_progress_cases',
            'riport.live',
            'riport.record',
            'riport.record_problem_type',
            'riport.record_gender',
            'riport.record_age',
            'riport.problem_type',
            'riport.is_crisis',
            'riport.problem_details',
            'riport.gender',
            'riport.employee_or_family_member',
            'riport.age',
            'riport.type_of_problem',
            'riport.language',
            'riport.place_of_receipt',
            'riport.source',
            'riport.valeo_workplace_1',
            'riport.valeo_workplace_2',
            'riport.hydro_workplace',
            'riport.pse_workplace',
            'riport.michelin_workplace',
            'riport.sk_battery_workplace',
            'riport.grupa_workplace',
            'riport.robert_bosch_workplace',
            'riport.gsk_workplace',
            'riport.johnson_and_johnson_workplace',
            'riport.syngenta_workplace',
            'riport.nestle_workplace',
            'riport.mahle_pl_workplace',
            'riport.lpp_workplace',
            'riport.amrest_workplace',
            'riport.gender_x_problem_type',
            'riport.age_x_problem_type',
            'riport.statistics_are_rounded',
            'riport.download',
            'eap-online.riports.download',
            'riport.no_data_to_enter',
            'what-is-new.headline',
            'what-is-new.contact',
            'what-is-new.watch_video',
            'what-is-new.go_to_reports',
            'common.reports',
            'riport.health_map',
            'common.workshops',
            'common.crisis_interventions',
            'common.prize_game',
            'common.customer_satisfaction',
            'common.program_usage',
            'riport.news',
            'riport.volume_request',
            'common.change-password',
            'common.logout',
            'common.back-to-admin',
            'riport.cumulated_info',
            'riport.cumulate',
        ];

        $translations = [];

        foreach ($keys as $key) {
            $translations[$key] = __($key, [], 'uk');
        }

        foreach ($keys as $key) {
            $english_translations[$key] = __($key, [], 'en');
        }

        Excel::store(new FileTranslationsExport($translations, $english_translations), 'file_translations_'.Carbon::now()->format('Y-m').'.xlsx', 'private');
    }
}
