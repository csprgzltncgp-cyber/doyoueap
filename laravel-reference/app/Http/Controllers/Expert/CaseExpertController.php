<?php

namespace App\Http\Controllers\Expert;

use App\Enums\CantAssignCaseReasonEnum;
use App\Enums\CaseExpertStatus;
use App\Enums\CompsychSurveyType;
use App\Mail\CantAssignCaseMail;
use App\Mail\NestleQuestionnaire;
use App\Models\Cases;
use App\Models\CaseValues;
use App\Models\Consultation;
use App\Models\EapOnline\EapAppointmentBooking;
use App\Models\EapOnline\EapExpertDayOff;
use App\Models\EapOnline\EapOnlineTherapyAppointment;
use App\Models\Inactivity;
use App\Models\InvoiceCaseData;
use App\Models\LanguageSkill;
use App\Models\User;
use App\Scopes\CountryScope;
use App\Services\CompsychSurveyService;
use App\Traits\CaseCloseTrait;
use App\Traits\EapOnline\OnlineTherapyTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CaseExpertController extends BaseExpertController
{
    use CaseCloseTrait;
    use OnlineTherapyTrait;

    public function list_in_progress()
    {
        $cases = Cases::query()
            ->whereNotIn('status', ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed', 'opened'])
            ->with(['values', 'values.input', 'experts', 'consultations', 'company', 'date', 'case_type', 'case_location', 'case_client_name'])
            ->whereHas('experts', fn (Builder $query) => $query->where('user_id', Auth::id())->whereNotIn('accepted', [CaseExpertStatus::REJECTED->value]))
            ->get()
            ->filter(function ($case): bool {
                /* IF someone accepeted the case and started working on it (accepeted = 1)
                but this person is not the same as the current expert, than filter out this case */
                if (! $case->case_accepted_expert()) {
                    return true;
                }

                return $case->case_accepted_expert()->id == Auth::id();
            });
        $cases->each(function ($item, $key): void {
            if ($item->status == 'opened' || $item->status == 'assigned_to_expert') {
                $item->setAttribute('percentage', 0);
            } else {
                $case_type = optional($item->case_type)->value;
                if (
                    $item->isCloseable()['closeable'] ||
                    (
                        /* MUNKAJOGI ESETKENÉL */
                        $case_type == 5 && (
                            $item->getRawOriginal('status') == 'employee_contacted' ||
                            $item->customer_satisfaction_not_possible ||
                            $item->customer_satisfaction
                        )
                    )
                    ||
                    (
                        /* EGYÉB ESETKNÉL */
                        $case_type == 4 && (
                            $item->getRawOriginal('status') == 'employee_contacted' ||
                            $item->customer_satisfaction_not_possible
                        )
                    )
                    ||
                    /* COACHING ESTEK */
                    ($case_type == 11 && $item->customer_satisfaction_not_possible)
                    ||
                    (
                        (
                            (in_array($case_type, [1, 2, 3]) && $item->customer_satisfaction != null) ||
                            (! in_array($case_type, [1, 2, 3]) && $item->customer_satisfaction_not_possible == 1)
                        )
                        &&
                        ($item->consultations->count() > 0)
                        &&
                        (! in_array($case_type, [1, 6, 7]))
                    )
                ) {
                    $item->setAttribute('percentage', 100);
                } elseif ($item->consultations->count() > 0 && $item->getRawOriginal('status') == 'employee_contacted') {
                    $item->setAttribute('percentage', 66);
                } elseif ($item->getRawOriginal('status') == 'employee_contacted') {
                    $item->setAttribute('percentage', 33);
                }
            }
        });

        $cases = $cases->sortByDesc(fn ($case, $key) => $case->date);

        return view('expert.cases.list.in_progress', ['cases' => $cases]);
    }

    // ESET MEGTEKINTÉSE
    public function view($id)
    {
        $case = Cases::query()->findOrFail($id);
        $language_skills = LanguageSkill::query()->get()
            ->sortBy(fn ($item) => optional($item->translation)->value);

        if (in_array(Auth::user()->id, $case->experts->pluck('pivot.user_id')->toArray()) && in_array($case->getRawOriginal('status'), ['assigned_to_expert', 'employee_contacted', 'client_unreachable', 'confirmed', 'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed', 'wos_answers'])) {

            $online_appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('case_id', $case->id)->get()->sortByDesc('id')->first();
            $intake_online_booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('case_id', $case->id)->get()->sortByDesc('id')->first();
            $consultation_type = optional($case->values->where('case_input_id', 24)->first())->value;

            $online_therapy_appointments = ($online_appointment_booking) ? $this->set_appointments($case->case_type->value, Auth::id()) : [];
            if ($case->isCloseable()['closeable'] && empty($case->confirmed_by)) {
                $this->user_notifications['always'] = __('popup.closeable-case', ['BUTTON' => Str::upper(__('common.close-case'))]);
            }

            if ($case->case_company_contract_holder() == 1 && $case->company->id == 227 && $case->case_type->value == 1) {
                $this->user_notifications['always'] = 'There is a new field. Please click on it. It is called "Questionnaire"';
            }

            // Set eap_consultation_deleted to false if it is true
            $case->update(['eap_consultation_deleted' => false]);

            return view('expert.cases.view', [
                'case' => $case,
                'online_appointment_booking' => $online_appointment_booking,
                'intake_online_booking' => $intake_online_booking,
                'online_therapy_appointments' => $online_therapy_appointments,
                'language_skills' => $language_skills,
                'consultation_type' => $consultation_type,
            ]);
        }

        return abort(403);
    }

    public function customerSatisfactionNotPossible(Request $request)
    {
        Cases::query()->where('cases.id', $request->case_id)->update([
            'customer_satisfaction_not_possible' => $request->checked,
        ]);
        $case = Cases::query()->findOrFail($request->case_id);

        return response()->json([
            'status' => 0,
            'checked' => $request->checked,
            'customerSatisfactionModal' => $case->shouldShowCustomerSatisfactionModal(),
        ]);
    }

    public function sendQuestionToOperator(Request $request)
    {
        $case = Cases::query()->findOrFail($request->case_id);
        $case->sendQuestionToOperator();
        $case->sendQuestionCopyToExpert($request->question);
        $case->sendQuestionToCountryEmail($request->question);

        return response()->json(['status' => 0]);
    }

    public function cantAssignCase(Request $request)
    {
        request()->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'reason' => ['required', 'string'],
            'days' => ['required_if:reason,'.CantAssignCaseReasonEnum::NOT_AVAILABLE->value],
        ]);

        $case = Cases::query()->findOrFail($request->case_id);

        /** @var User $user */
        $user = Auth::user();

        $case->experts()->syncWithoutDetaching([$user->id => ['accepted' => CaseExpertStatus::REJECTED->value]]);

        if (request()->input('reason') === CantAssignCaseReasonEnum::ETHICAL_REASONS->value || request()->input('reason') === CantAssignCaseReasonEnum::NOT_AVAILABLE->value) {
            $experts_assigned_before = $case->experts()->pluck('users.id');
            $available_experts = $case->getAvailableExperts($experts_assigned_before);

            if ($available_experts->count() > 0) {
                $request->merge(['expert_id' => $available_experts->first()->id]);
                User::assignCase($request->case_id, $request->expert_id);
            }

            if (request()->input('reason') === CantAssignCaseReasonEnum::NOT_AVAILABLE->value) {
                $user->update([
                    'active' => false,
                ]);

                Inactivity::query()->updateOrCreate([
                    'user_id' => $user->id,
                ], [
                    'until' => Carbon::now()->addDays(request()->input('days'))->endOfDay()->subMinute(),
                ]);
            }

            return response()->json(['status' => 0]);
        }

        if (request()->input('reason') === CantAssignCaseReasonEnum::PROFESSIONAL_REASONS->value) {
            Mail::to('maria.szabo@cgpeu.com')->send(new CantAssignCaseMail($case, $user));
            Mail::to('klaudia.janosik@cgpeu.com')->send(new CantAssignCaseMail($case, $user));

            return response()->json(['status' => 0]);
        }

        return response()->json(['status' => 0]);
    }

    public function editConsultationDate(Request $request)
    {
        $case = Cases::query()->findOrFail($request->case_id);
        if ($case->consultations->where('id', '!=', $request->consultation_id)->whereBetween('created_at', [Carbon::parse($request->consultation_date)->startOfDay(), Carbon::parse($request->consultation_date)->endOfDay()])->count()) {
            return ['consultation_today_exists' => true];
        }

        $consultation = Consultation::query()->where('id', $request->consultation_id)->first();
        $consultation->update([
            'created_at' => $request->consultation_date,
        ]);

        if ($request->eap_user_id) {
            $consultation->send_notification('edit', $request->eap_user_id, $case);
        }

        return response()->json(['status' => 0, 'more_consultation_can_be_added' => $case->can_add_more_consultation(false)]);
    }

    public function wosSurveyClicked(Request $request)
    {
        Cases::query()->where('id', $request->case_id)->update(['wos_survey_clicked' => true]);

        return response()->json(['status' => 0]);
    }

    public function set_phq9_score()
    {
        request()->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'sum' => ['required', 'numeric', 'min:0', 'max:27'],
            'type' => ['required'],
        ]);

        $case = Cases::query()->findOrFail(request()->input('case_id'));
        if (request()->input('type') == 'opening') {
            $case->phq9_opening = request()->input('sum');
        } else {
            $case->phq9_closing = request()->input('sum');
        }

        $case->save();

        return response()->json(['status' => 0]);
    }

    public function send_nestle_questionnaire(): void
    {
        request()->validate([
            'email' => ['email', 'required'],
            'case_id' => ['required', 'exists:cases,id'],
        ]);

        $case = Cases::query()->findOrFail(request()->input('case_id'));
        $case->nestle_questionnaire_sent = true;
        $case->save();

        Mail::to(request()->input('email'))->send(new NestleQuestionnaire($case->case_client_name != null ? $case->case_client_name->getValue() : null));
    }

    public function deleteConsultation()
    {
        request()->validate([
            'consultation_id' => ['required', 'exists:consultations,id'],
        ]);

        $consultation = Consultation::query()->where('id', request()->input('consultation_id'))->first();

        if ($consultation) {
            $consultation->delete_consultation(
                (int) request()->input('booking_id') ?: null,
                (string) request()->input('room_id') ?: null,
                (int) request()->input('eap_user_id') ?: null,
            );
        }

        return response()->json(['status' => 0, 'case_deleted' => 0]);
    }

    public function delete_online_consultation()
    {
        request()->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'booking_id' => ['required'],
        ]);

        $case = Cases::query()->where('id', request()->input('case_id'))->first();
        $consultation = $case->consultations()->orderByDesc('id')->first();

        // IF the deleted consultation is the first, than delete the entire case, else only the consultation
        $consultation->delete_consultation(
            (int) request()->input('booking_id'),
            (string) request()->input('room_id') ?: null,
            (int) request()->input('eap_user_id'),
        );

        $case_deleted = (Cases::query()->where('id', request()->input('case_id'))->exists()) ? 0 : 1;

        return response()->json(['status' => 0, 'case_deleted' => $case_deleted]);
    }

    public function customer_satisfaction(Request $request)
    {
        Cases::query()->where('cases.id', $request->case_id)->update([
            'customer_satisfaction' => $request->score,
        ]);

        return response()->json(['status' => 0]);
    }

    public function assingNewValueToCaseInput(Request $request)
    {
        $case = Cases::query()->findOrFail($request->case_id);

        if (empty(Auth::user()->country_id)) {
            if ($case->country_id != Auth::user()->expert_data->country_id) {
                return response()->json(['status' => 1]);
            }
            CaseValues::query()->where('case_id', $request->case_id)->where('case_input_id', $request->input_id)->update([
                'value' => $request->value,
            ]);

            return response()->json(['status' => 0]);
        }
        if ($case->country_id != Auth::user()->country_id) {
            return response()->json(['status' => 1]);
        }
        CaseValues::query()->where('case_id', $request->case_id)->where('case_input_id', $request->input_id)->update([
            'value' => $request->value,
        ]);

        return response()->json(['status' => 0]);
    }

    public function close_case($id)
    {
        $case = Cases::query()->findOrFail($id);
        $online_appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('case_id', $case->id)->exists();
        $case_permission = optional($case->values->where('case_input_id', 7)->first())->value;

        if ($closeable = $case->isCloseable(null, $online_appointment_booking)['closeable']) {
            $case->closed_by_expert = Auth::id();
            $case->status = 'confirmed';
            $case->confirmed_by = Auth::id();
            $case->confirmed_at = Carbon::now('Europe/Budapest');
            $case->save();

            $this->exclude_client_from_online_therapy($case->id);
            $this->set_intake_colsed_at_date($case->id);

            $this->send_pulso_outtake_email($case);

            // Az LPP cég esetén az elégedettségi pontszámhoz tartozó e-mail / SMS küldése
            if ($case->company_id == 843) {
                $this->lpp_customer_satisfaction($case);
            }

            // IF counsultation type is chat, delete all chat messages in the eap_online databse belongig to the case
            $this->delete_chat_messages($case);

            $permission_id = optional($case->case_type)->value;
            $duration = $permission_id ? (int) optional($case->values->where('case_input_id', 22)->first())->input_value->value : null;
            $consultation_count = $case->consultations->count();

            // IF online appointment booking, than check if there are deleted consultation within 48 hour.
            if ($online_appointment_booking) {
                $consultation_count += $case->get_deleted_within_48_hour_consultations_count();
            }

            // Well Being Coaching (16)
            if ((int) $case_permission === 16) {

                InvoiceCaseData::query()->firstOrCreate([
                    'case_identifier' => $case->case_identifier,
                    'consultations_count' => 1,
                    'expert_id' => Auth::user()->id,
                    'duration' => 30,
                    'permission_id' => (int) $case->case_type->value,
                ]);

                if ($consultation_count > 1) {
                    InvoiceCaseData::query()->firstOrCreate([
                        'case_identifier' => $case->case_identifier,
                        'consultations_count' => $consultation_count - 1,
                        'expert_id' => Auth::user()->id,
                        'duration' => 15,
                        'permission_id' => (int) $case->case_type->value,
                    ]);
                }

            } else {
                InvoiceCaseData::query()->firstOrCreate([
                    'case_identifier' => $case->case_identifier,
                    'consultations_count' => $consultation_count,
                    'expert_id' => Auth::user()->id,
                    'duration' => (int) $duration,
                    'permission_id' => (int) $case->case_type->value,
                ]);
            }

            // Send compsych (3) survey for psychological cases
            $company = $case->company()->withoutGlobalScope(CountryScope::class)->first();
            if ($company && (int) $company->org_datas->first()->contract_holder_id === 3 && (int) $case->case_type->value === 1 && $case->created_at->gte(Carbon::parse('2025-10-22 00:01:00'))) {
                $compsych_survey_form_service = new CompsychSurveyService(CompsychSurveyType::CASE_CLOSED);
                $compsych_survey_form_service->send_mail(
                    $case->values->where('case_input_id', 4)->first()->value, // username
                    $case->values->where('case_input_id', 18)->first()->value, // email
                    $case->case_identifier,
                );
            }

            return response()->json(['status' => 0, 'case' => ['closeable' => $closeable]]);
        }

        return response()->json(['status' => 0, 'case' => $case->isCloseable()]);
    }

    public function caseInterrupted(int $id): JsonResponse
    {
        return $this->interrupt_case($id);
    }

    public function clientUnreachable(Request $request)
    {
        $case = Cases::query()->findorFail($request->case_id);
        $case->update([
            'status' => 'client_unreachable_confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => Carbon::now('Europe/Budapest'),
            'closed_by_expert' => Auth::id(),
        ]);
        $case->save();

        $duration = optional($case->values->where('case_input_id', 22)->first())->input_value->value;

        $consultation_count = $case->consultations->count();

        // IF online appointment booking, than check if there are deleted consultation within 48 hour.
        $online_appointment_booking = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->where('case_id', $case->id)->exists();

        if ($online_appointment_booking) {
            $consultation_count += $case->get_deleted_within_48_hour_consultations_count();
        }

        InvoiceCaseData::query()->firstOrCreate([
            'case_identifier' => $case->case_identifier,
            'consultations_count' => $consultation_count,
            'expert_id' => Auth::user()->id,
            'duration' => (int) $duration,
            'permission_id' => (int) $case->case_type->value,
        ]);

        $this->exclude_client_from_online_therapy($request->case_id);
        $this->set_intake_colsed_at_date($case->id);

        // IF counsultation type is chat, delete all chat messages in the eap_online databse belongig to the case
        $this->delete_chat_messages($case);

        return response()->json(['status' => 0]);
    }

    private function set_appointments(int $permission, $expert)
    {
        $result = collect([]);

        $appointments = EapOnlineTherapyAppointment::query()
            ->notCustom()
            ->where('permission_id', $permission)
            ->orderBy('from')
            ->where('expert_id', $expert)
            ->get()->each(function ($appointment): void {
                $appointment->setAttribute(
                    'reserved_dates',
                    EapAppointmentBooking::query()
                        ->where('online_therapy_appointment_id', $appointment->id)
                        ->pluck('date')
                );
            });

        $days = CarbonPeriod::create(now()->addDay()->startOfDay(), now()->addMonth()->endOfDay())->toArray();

        // Filter out reserved appointments
        foreach ($days as $day) {
            $appointments->each(function ($appointment) use ($day, &$result): void {
                $day_number = Carbon::parse($day)->dayOfWeek;

                if ((int) $appointment->day != $day_number) {
                    return;
                }

                if ($appointment->reserved_dates->contains(fn ($date): bool => Carbon::parse($date)->format('Y-m-d') == Carbon::parse($day)->format('Y-m-d'))) {
                    return;
                }

                $result->push([
                    'appointment_id' => $appointment->id,
                    'expert_id' => $appointment->expert_id,
                    'date' => $day->format('Y.m.d'),
                    'from' => $appointment->from,
                    'to' => $appointment->to,
                ]);
            });
        }

        // Filter out available appointments based on expert days off
        if ($expert) {
            $expert_day_off = EapExpertDayOff::query()->where('expert_id', $expert)->get();
            $result = $result->filter(function (array $item) use ($expert_day_off): bool {
                foreach ($expert_day_off as $day_off) {
                    if (Carbon::parse(Str::replace('.', '-', $item['date'].' '.$item['from']))->between(Carbon::parse($day_off->from), Carbon::parse($day_off->to))) {
                        return false;
                    }
                }

                return true;
            });
        }

        // Filter out available appointments based on current time of day(before or after noon)
        if (! Carbon::now()->lt(Carbon::parse('12:00'))) {
            $result = $result->filter(fn (array $item): bool => ! Carbon::parse(Str::replace('.', '-', $item['date'].' '.$item['from']))->lte(Carbon::parse('12:00')->addDay()));
        }

        return $result;
    }
}
