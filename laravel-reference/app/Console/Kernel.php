<?php

namespace App\Console;

use App\Console\Commands\ActivateCustomerSatisfactions;
use App\Console\Commands\ActivateEapRiports;
use App\Console\Commands\ActivateRiports;
use App\Console\Commands\CloseCasesWith6OrLessConsultation;
use App\Console\Commands\CloseCloseableTelusCases;
use App\Console\Commands\ConfirmCasesAfterOneMonth;
use App\Console\Commands\ContractExpireNotification;
use App\Console\Commands\CountFixedPriceExpertConsultations;
use App\Console\Commands\CreateContractHolderRiports;
use App\Console\Commands\CreateCustomerSatisfactions;
use App\Console\Commands\CreateDashboardAffiliateDatas;
use App\Console\Commands\CreateDashboardLifeworksDatas;
use App\Console\Commands\CreateEapRiports;
use App\Console\Commands\CreateRiports;
use App\Console\Commands\CreateTotalRiports;
use App\Console\Commands\DataBaseBackup;
use App\Console\Commands\DeleteCasesAfter3Months;
use App\Console\Commands\DeleteSensitiveDataAfter3Months;
use App\Console\Commands\GenerateConsultationUsageData;
use App\Console\Commands\InactivateExpertsWithMissingExpertData;
use App\Console\Commands\Send24HourAssignedCases;
use App\Console\Commands\Send24HourSpanishCases;
use App\Console\Commands\SendAccountAdminAlertEmails;
use App\Console\Commands\SendCompanyUtilizationMail;
use App\Console\Commands\SendCompsychSurveyEmail;
use App\Console\Commands\SendVolumeRequest;
use App\Console\Commands\SetDirectInvoiceHeadcountFromVolumeRequest;
use App\Console\Commands\SetExpertCanAcceptMoreCasesProperty;
use App\Console\Commands\SetPrezeroInterruptedAndUnreachableCaseStatuses;
use App\Jobs\CreateNewWorkshopOrCrisisCases;
use App\Jobs\Send24HoursCaseEmails;
use App\Jobs\Send3MonthsCaseEmails;
use App\Jobs\Send48HoursWorkshopReminderEmails;
use App\Jobs\Send5DaysCaseEmails;
use App\Jobs\SendLowCustomerSatisfactionEmail;
use App\Jobs\VolumeRequest\CreateVolumeRequestsJob;
use App\Services\DateService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DataBaseBackup::class,
        DeleteCasesAfter3Months::class,
        DeleteSensitiveDataAfter3Months::class,
        ConfirmCasesAfterOneMonth::class,
        CreateContractHolderRiports::class,
        CreateCustomerSatisfactions::class,
        InactivateExpertsWithMissingExpertData::class,
        CreateRiports::class,
        ActivateRiports::class,
        CreateEapRiports::class,
        ActivateEapRiports::class,
        ActivateCustomerSatisfactions::class,
        SendAccountAdminAlertEmails::class,
        ContractExpireNotification::class,
        GenerateConsultationUsageData::class,
        CreateTotalRiports::class,
        SetExpertCanAcceptMoreCasesProperty::class,
        CreateDashboardAffiliateDatas::class,
        CreateDashboardLifeworksDatas::class,
        CloseCloseableTelusCases::class,
        SendVolumeRequest::class,
        SetPrezeroInterruptedAndUnreachableCaseStatuses::class,
        SetDirectInvoiceHeadcountFromVolumeRequest::class,
        Send24HourAssignedCases::class,
        SendCompanyUtilizationMail::class,
        CountFixedPriceExpertConsultations::class,
        Send24HourSpanishCases::class,
        SendCompsychSurveyEmail::class,
        CloseCasesWith6OrLessConsultation::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (config('app.env') == 'production' || config('app.env') == 'local') {
            $schedule->job(new CreateNewWorkshopOrCrisisCases)->daily();
            $schedule->job(new SendLowCustomerSatisfactionEmail)->daily();
            $schedule->job(new Send3MonthsCaseEmails)->daily();
            $schedule->job(new Send5DaysCaseEmails)->daily();
            $schedule->job(new Send24HoursCaseEmails)->daily();
            $schedule->job(new Send48HoursWorkshopReminderEmails)->daily();
            $schedule->command('cases:close-closeable-telus-cases')->daily();
            $schedule->command('app:send-volume-request')->daily()->when(function (): bool {
                if (DateService::is_work_day_of_month(3)) {
                    return true;   // Send email on the 3rd work day of the month
                }

                return false;
            })->at('07:00');
            $schedule->command('app:set-direct-invoice-headcount-from-volume-request')->daily()->when(function (): bool {
                return DateService::is_day_of_month(4, 3); // Set direct invoice item headcount on the 4th day afternoon of the month (Check if 3rd day is weekend)
            })->at('12:00');
            $schedule->job(new CreateVolumeRequestsJob)->when(fn () => Carbon::now()->startOfMonth()->isToday())->at('02:00');

            $schedule->command('expert:set_can_accept_more_cases')->twiceDaily(1, 13);
            $schedule->command('expert:reactive-inactive')->daily();

            $schedule->command('cases:close-closeable-cases')->daily();
            $schedule->command('database:backup')->daily();
            $schedule->command('contract-expire-notification:email')->daily();
            $schedule->command('telescope:prune --hours=48')->daily();
            $schedule->command('app:send-compsych-survey-email')->dailyAt('23:50');

            $schedule->command('app:send-24hour-assigned-cases')->weeklyOn(7, '23:58');
            $schedule->command('app:send24-hour-spanish-cases')->weeklyOn(7, '23:58');

            $schedule->command('cases:close-cases-with6-or-less-consultation')->monthlyOn(30, '23:55');
            $schedule->command('app:send-company-utilization-mail')->monthly()->at('08:00');
            $schedule->command('riports:create-contract-holder')->monthly();
            $schedule->command('riports:create-custom-company')->monthly();
            $schedule->command('riports:create')->monthly();
            $schedule->command('customer-satisfactions:create')->monthly();
            $schedule->command('generate:consultation-usage-data')->monthly();
            $schedule->command('create:live-webinar-invoice-items')->monthly();

            $schedule->command('app:count-fixed-price-expert-consultations')->lastDayOfMonth('23:40');

            $schedule->command('experts:inactivate-with-missing-expert-data')->monthlyOn(1, '03:00');
            $schedule->command('create:direct-invoices')->monthlyOn(1, '04:00');
            $schedule->command('create:completion-certificates')->monthlyOn(1, '04:15');
            $schedule->command('create:envelopes')->monthlyOn(1, '04:30');
            $schedule->command('create:contract-holder-direct-invoices')->monthlyOn(1, '04:35');
            $schedule->command('create:dashboard-affiliate-datas')->monthlyOn(1, '04:40');
            $schedule->command('cases:delete-sensitive-data-after-3-months')->monthlyOn(1, '04:50');
            // $schedule->command('cases:delete-after-3-months')->monthlyOn(1, '04:50');
            $schedule->command('riport:create-total-riport')->monthlyOn(1, '05:00');

            $schedule->command('account-admins:sent-alert-emails')->when(fn () => Carbon::now()->endOfMonth()->subWeek()->isToday())->at('08:00');

            $schedule->command('create:contract-holder-direct-invoices')->when(fn () => Carbon::now()->endOfMonth()->subDay()->isToday())->at('04:35');

            $schedule->command('cases:confirm-after-1-month')->lastDayOfMonth('23:30');

            $schedule->command('eap-riports:create')->quarterly();
            $schedule->command('riports:set-prezero-interrupted-and-unreachable-case-statuses')->quarterly()->at('00:30');
            $schedule->command('riports:activate')->quarterly()->at('01:00');
            $schedule->command('eap-riports:activate')->quarterly()->at('01:00');
            $schedule->command('customer-satisfactions:activate')->quarterly()->at('01:00');

            $schedule->call(function (): void {
                Artisan::call('cache:clear');
            })->quarterly()->at('02:00');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
