<?php

namespace App\Jobs;

use App\Enums\OtherActivityType;
use App\Models\Cases;
use App\Models\CrisisCase;
use App\Models\EapOnline\OnsiteConsultation;
use App\Models\EapOnline\OnsiteConsultationDate;
use App\Models\EapOnline\OnsiteConsultationDateAppointment;
use App\Models\OtherActivity;
use App\Models\PrizeGame\Game;
use App\Models\Riport;
use App\Models\RiportValue;
use App\Models\WorkshopCase;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateRiportForCompany implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $company, public $from, public $to) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $riport = Riport::query()->create([
            'from' => $this->from,
            'to' => $this->to,
            'company_id' => $this->company->id,
            'is_active' => false,
        ]);

        // generate cases data
        if (((is_countable($cases = $this->get_cases_between_dates($this->from, $this->to, $this->company)) ? count($cases = $this->get_cases_between_dates($this->from, $this->to, $this->company)) : 0) > 0)) {
            foreach ($cases as $case) {
                $this->generate_riport_values($case, $riport);
            }
        }

        // generate workshop cases data
        if ((is_countable($workshop_cases = $this->get_workshop_cases_between_dates($this->from, $this->to, $this->company)) ? count($workshop_cases = $this->get_workshop_cases_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($workshop_cases as $workshop_case) {
                $this->generate_workshop_values($workshop_case, $riport);
            }
        }

        // generate orientation data
        if ((is_countable($orientations = $this->get_orientation_between_dates($this->from, $this->to, $this->company)) ? count($orientations = $this->get_orientation_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($orientations as $orientation) {
                $this->generate_orientation_values($orientation, $riport);
            }
        }

        // generate health day data
        if ((is_countable($health_days = $this->get_health_day_between_dates($this->from, $this->to, $this->company)) ? count($health_days = $this->get_health_day_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($health_days as $health_day) {
                $this->generate_health_day_values($health_day, $riport);
            }
        }

        // generate expert outplacement data
        if ((is_countable($expert_outplacements = $this->get_expert_outplacement_between_dates($this->from, $this->to, $this->company)) ? count($expert_outplacements = $this->get_expert_outplacement_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($expert_outplacements as $expert_outplacement) {
                $this->generate_expert_outplacement_values($expert_outplacement, $riport);
            }
        }

        // generte prizegame data
        if ((is_countable($prizegames = $this->get_prizegames_between_dates($this->from, $this->to, $this->company)) ? count($prizegames = $this->get_prizegames_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($prizegames as $prizegame) {
                $this->generate_prizegame_values($prizegame, $riport);
            }
        }

        // generate crisis data
        if ((is_countable($cisis_interventions = $this->get_crisis_cases_between_dates($this->from, $this->to, $this->company)) ? count($cisis_interventions = $this->get_crisis_cases_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($cisis_interventions as $crisis) {
                $this->generate_crisis_values($crisis, $riport);
            }
        }

        // generate onsite consultation data
        if ((is_countable($onsite_consultations = $this->get_onsite_consultations_between_dates($this->from, $this->to, $this->company)) ? count($onsite_consultations = $this->get_onsite_consultations_between_dates($this->from, $this->to, $this->company)) : 0) > 0) {
            foreach ($onsite_consultations as $onsite_consultation) {
                $this->generate_onsite_consultation_values($onsite_consultation, $riport);
            }
        }

        Log::info('[JOB][CreateRiportForCompany] New riport created for: '.$this->company->name);
    }

    private function get_orientation_between_dates($from, $to, $company)
    {
        return OtherActivity::query()
            ->where('company_id', $company->id)
            ->where('type', OtherActivityType::TYPE_ORIENTATION)
            ->whereNotNull('participants')
            ->whereBetween('closed_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->get();
    }

    private function get_health_day_between_dates($from, $to, $company)
    {
        return OtherActivity::query()
            ->where('company_id', $company->id)
            ->where('type', OtherActivityType::TYPE_HEALTH_DAY)
            ->whereNotNull('participants')
            ->whereBetween('closed_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->get();
    }

    private function get_expert_outplacement_between_dates($from, $to, $company)
    {
        return OtherActivity::query()
            ->where('company_id', $company->id)
            ->where('type', OtherActivityType::TYPE_EXPERT_OUTPLACEMENT)
            ->whereNotNull('participants')
            ->whereBetween('closed_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->get();
    }

    private function generate_orientation_values($orientation, $riport): void
    {
        RiportValue::query()->create([
            'type' => RiportValue::TYPE_ORIENTATION_NUMBER_OF_PARTICIPANTS,
            'value' => $orientation->participants,
            'country_id' => $orientation->country_id,
            'riport_id' => $riport->id,
        ]);
    }

    private function generate_health_day_values($health_day, $riport): void
    {
        RiportValue::query()->create([
            'type' => RiportValue::TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS,
            'value' => $health_day->participants,
            'country_id' => $health_day->country_id,
            'riport_id' => $riport->id,
        ]);
    }

    private function generate_expert_outplacement_values($expert_outplacement, $riport): void
    {
        RiportValue::query()->create([
            'type' => RiportValue::TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS,
            'value' => $expert_outplacement->participants,
            'country_id' => $expert_outplacement->country_id,
            'riport_id' => $riport->id,
        ]);
    }

    private function get_workshop_cases_between_dates($from, $to, $company)
    {
        return WorkshopCase::query()
            ->where('company_id', $company->id)
            ->whereNotNull('number_of_participants')
            ->whereBetween('date', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->where('status', 3)
            ->get();
    }

    private function get_prizegames_between_dates($from, $to, $company)
    {
        return Game::query()
            ->whereBetween('to', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->whereHas('content', function ($query) use ($company): void {
                $query->where('company_id', $company->id)->whereNotNull('country_id');
            })
            ->get();
    }

    private function get_crisis_cases_between_dates($from, $to, $company)
    {
        return CrisisCase::query()
            ->where('company_id', $company->id)
            ->whereNotNull('number_of_participants')
            ->whereBetween('date', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->where('status', 3)
            ->get();
    }

    private function get_onsite_consultations_between_dates($from, $to, $company)
    {
        return OnsiteConsultation::query()
            ->with(['place', 'languages', 'dates' => fn ($q) => $q->whereBetween('date', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])])
            ->whereHas('dates', function ($query) use ($from, $to): void {
                $query->whereBetween('date', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
            })
            ->where('company_id', $company->id)
            ->get();
    }

    private function generate_workshop_values($workshop_case, $riport): void
    {
        RiportValue::query()->create([
            'type' => RiportValue::TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS,
            'value' => $workshop_case->number_of_participants,
            'country_id' => $workshop_case->workshop->country_id,
            'riport_id' => $riport->id,
        ]);
    }

    private function generate_prizegame_values($prizegame, $riport): void
    {
        RiportValue::query()->create([
            'type' => RiportValue::TYPE_PRIZEGAME_NUMBER_OF_PARTICIPANTS,
            'value' => $prizegame->guesses->count(),
            'country_id' => $prizegame->content->country_id,
            'riport_id' => $riport->id,
        ]);
    }

    private function generate_crisis_values($crisis_case, $riport): void
    {
        RiportValue::query()->create([
            'type' => RiportValue::TYPE_CRISIS_NUMBER_OF_PARTICIPANTS,
            'value' => $crisis_case->number_of_participants,
            'country_id' => $crisis_case->crisis_intervention->country_id,
            'riport_id' => $riport->id,
        ]);
    }

    private function get_cases_between_dates($from, $to, $company)
    {
        return Cases::query()
            ->where('company_id', $company->id)
            ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->get();
    }

    private function generate_onsite_consultation_values($onsite_consultation, $riport): void
    {
        $onsite_consultation->dates->each(function (OnsiteConsultationDate $date) use ($onsite_consultation, $riport): void {
            $date->appointments->each(function (OnsiteConsultationDateAppointment $appointment) use ($onsite_consultation, $riport): void {
                RiportValue::query()->create([
                    'type' => RiportValue::TYPE_ONSITE_CONSULTATION_STATUS,
                    'value' => ($appointment->user_id) ? 'booked' : 'available',
                    'country_id' => $onsite_consultation->country_id,
                    'riport_id' => $riport->id,
                    'is_ongoing' => ($appointment->user_id) ? 0 : 1,
                ]);

                RiportValue::query()->create([
                    'type' => RiportValue::TYPE_ONSITE_CONSULTATION_SITE,
                    'value' => $onsite_consultation->onsite_consultation_place_id,
                    'country_id' => $onsite_consultation->country_id,
                    'riport_id' => $riport->id,
                    'is_ongoing' => ($appointment->user_id) ? 0 : 1,
                ]);
            });
        });
    }

    private function generate_riport_values($case, $riport): void
    {
        $ongoing = in_array($case->getRawOriginal('status'), ['assigned_to_expert', 'employee_contacted', 'opened']);
        $connection_id = Str::uuid()->toString();

        foreach ($case->values()->get() as $value) {
            if (in_array($value->value, [null, '-', 'XXX'])) {
                continue;
            }

            if (! in_array($value->case_input_id, [3, 5, 6, 7, 9, 10, 11, 12, 16, 24, 25, 26, 27, 28, 30, 31, 32, 33, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 52, 59, 60, 61, 71, 72, 74, 75, 76, 77, 78, 79, 80, 88, 90, 92])) {
                continue;
            }

            RiportValue::query()->create([
                'type' => $value->case_input_id,
                'value' => $value->value,
                'country_id' => $case->country_id,
                'riport_id' => $riport->id,
                'connection_id' => in_array($value->case_input_id, [7, 10, 11]) ? $connection_id : null,
                'is_ongoing' => $ongoing,
            ]);
        }

        RiportValue::query()->create([
            'type' => RiportValue::TYPE_CONSULTATION_NUMBER,
            'value' => $case->consultations->count(),
            'country_id' => $case->country_id,
            'riport_id' => $riport->id,
            'is_ongoing' => $ongoing,
        ]);

        RiportValue::query()->create([
            'type' => RiportValue::TYPE_STATUS,
            'value' => $case->getRawOriginal('status'),
            'country_id' => $case->country_id,
            'riport_id' => $riport->id,
            'is_ongoing' => $ongoing,
        ]);

        RiportValue::query()->create([
            'type' => RiportValue::TYPE_CASE_CREATED_AT,
            'value' => Carbon::parse($case->created_at)->format('Y-m-d'),
            'country_id' => $case->country_id,
            'riport_id' => $riport->id,
            'is_ongoing' => $ongoing,
        ]);

        if ($case->company->org_datas) {

            // Morneau Shepell specific value
            if ($case->case_company_contract_holder() == 1 && $case->consultations->count() > 0) {
                RiportValue::query()->create([
                    'type' => RiportValue::TYPE_CONSULTATION_DATES,
                    'value' => implode(',', $case->consultations->map(fn ($model) => Carbon::parse($model->created_at)->format('Y-m-d'))->toArray()),
                    'country_id' => $case->country_id,
                    'riport_id' => $riport->id,
                ]);
            }

            // Compsych specific value
            if ($case->case_company_contract_holder() == 3 && $case->consultations->count() > 0) {
                RiportValue::query()->create([
                    'type' => RiportValue::TYPE_FIRST_CONSULTATION,
                    'value' => Carbon::parse($case->consultations->sortBy('created_at')->first()->created_at)->format('Y-m-d'),
                    'country_id' => $case->country_id,
                    'riport_id' => $riport->id,
                ]);
            }

            // Optum specific value
            if ($case->case_company_contract_holder() == 4) {
                RiportValue::query()->create([
                    'type' => RiportValue::TYPE_CASE_CLOSED_AT,
                    'value' => Carbon::parse($case->confirmed_at)->format('Y-m-d'),
                    'country_id' => $case->country_id,
                    'riport_id' => $riport->id,
                ]);
            }
        }
    }
}
