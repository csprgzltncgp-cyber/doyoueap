<?php

namespace App\Traits\EapOnline;

use App\Enums\CompsychSurveyType;
use App\Mail\AssignCaseMail;
use App\Models\Cases;
use App\Models\Company;
use App\Models\Consultation;
use App\Models\User;
use App\Models\WosAnswers;
use App\Services\CompsychSurveyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

trait OnlineTherapyTrait
{
    public function exclude_client_from_online_therapy($case_id): void
    {
        $case = Cases::query()->findOrFail($case_id);
        $client = DB::connection('mysql_eap_online')->table('online_appointment_bookings')->whereNotNull('case_id')->where('case_id', $case->id)->first();
        if (! $client) {
            return;
        }

        if (! $case->isCloseable()['closeable'] && ! $case->closed_by_expert && ! $case->status == 'client_unreachable') {
        }
    }

    public function set_intake_colsed_at_date($case_id): void
    {
        $case = Cases::query()->findOrFail($case_id);
        $intake_booking = DB::connection('mysql_eap_online')->table('intake_bookings')->where('case_id', $case->id)->first();

        if ($intake_booking) {
            DB::connection('mysql_eap_online')->table('intake_bookings')->where('case_id', $case->id)->update([
                'case_closed_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function get_case_data(Request $request): array
    {
        $case_datas = collect([]);
        collect($request['cases_data'])->map(function (array $item) use (&$case_datas): void {

            $case = Cases::query()->with('consultations')->where('id', $item['case_id'])->first();

            if ($case) {
                // Get avaible permission details (pivot = permission_x_company table)
                $available_permissions = Company::query()
                    ->where('id', $case->company_id)
                    ->first()
                    ->permissions()
                    ->where('permission_id', $case->values->where('case_input_id', 7)->first()->value) // 7 - problem type
                    ->first()->getRelationValue('pivot');

                // Get the first consultation that is still upcoming compared to the current date and time
                if ($item['online_appointment']) { // Online
                    $next_consultation = (is_null($item['consultation_end'])) ?
                    $case->consultations()
                        ->orderByDesc('created_at')
                        // ->withTrashed()
                        ->first() : null;
                } else { // Intake
                    $next_consultation = $case->consultations()
                        ->orderByDesc('created_at')
                        ->when($item['consultation_end'], function ($query) use ($item): void {
                            $query->where('created_at', '>', $item['consultation_end']);
                        })
                        ->first();
                }

                // If there is no next consultation get consultation type from case input
                $consultation_type = ($next_consultation && ! $next_consultation->deleted_at) ?
                    (int) $next_consultation->case->values->where('case_input_id', 24)->first()->value :
                    (int) Cases::query()->where('id', $case->id)->first()->values->where('case_input_id', 24)->first()->value;

                $used_consultations_number = Consultation::query()
                    ->where('case_id', $case->id)
                    ->where('permission_id', $case->values->where('case_input_id', 7)->first()->value) // 7 - problem type
                    ->when($item['online_appointment'], fn ($query) => $query->withTrashed()) // When online chat or video chat booking count soft deleted consultations as used
                    ->count();

                // Set case status by priority
                $case_status = null;
                
                // Next/last consultation date is in the past the case reached the available consultation limit
                if ($next_consultation->created_at->addMinutes(50)->isPast() && ($used_consultations_number >= $available_permissions->number)) {
                    $case_status = 'confirmed';
                }

                // Expert closed the case
                if (!$case_status && !in_array($case->getRawOriginal('status'), ['assigned_to_expert', 'employee_contacted', 'opened'])) {
                    $case_status = 'confirmed';
                } 
                
                if (!$case_status && $next_consultation && ! $next_consultation->deleted_at) {
                    $case_status = 'in_progress';
                }

                if (!$case_status && $used_consultations_number < $available_permissions->number) {
                    $case_status = 'in_progress';
                }

                // If next_consultation exists but it is deleted(soft) or the date is in the past, then set it to null
                if ($next_consultation && ($next_consultation->deleted_at || Carbon::now()->gt($next_consultation->created_at->addMinutes(50)))) {
                    $next_consultation = null;
                }

                $case_datas->push([
                    'case_id' => $case->id,
                    'case_status' => $case_status,
                    'expert_id' => optional($case->experts->where('pivot.accepted', 1)->first())->id,
                    'expert_name' => optional($case->experts->where('pivot.accepted', 1)->first())->name,
                    'permission_id' => (int) $case->values->where('case_input_id', 7)->first()->value, // 7 - problem type
                    'customer_satisfaction' => (bool) $case->customer_satisfaction,
                    'online_appointment' => $item['online_appointment'],
                    'online_appointment_booking_id' => ($item['online_appointment']) ? $item['online_appointment_booking_id'] : null,
                    'consultation_type' => $consultation_type,
                    'next_consultation' => [
                        'consultation_id' => optional($next_consultation)->id,
                        'type' => $consultation_type,
                        'date' => ($next_consultation) ? Carbon::parse($next_consultation->created_at)->format('Y-m-d H:i') : null,
                    ],
                    'room_id' => $item['room_id'],
                ]);
            }
        });

        return $case_datas->sortBy(fn ($item): int =>
            // Sort by consultation date ASC. Put null dates last.
            $item['next_consultation']['date'] === null ? PHP_INT_MAX : Carbon::parse($item['next_consultation']['date'])->timestamp)->toArray();
    }

    public function delete_case_consultations(Request $request): bool
    {
        $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'user_id' => ['required'],
            'permission_id' => ['required', 'exists:permissions,id'],
            'created_at' => ['required'],
        ]);

        $consultation = Consultation::query()
            ->where('case_id', $request['case_id'])
            ->where('user_id', $request['user_id'])
            ->where('permission_id', $request['permission_id'])
            ->where('created_at', $request['created_at'])
            ->orderByDesc('id')
            ->first();

        return ($request['force_delete']) ? $consultation->forceDelete() : $consultation->delete();
    }

    public function create_case_consultation(Request $request): void
    {
        $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'user_id' => ['required'],
            'permission_id' => ['required', 'exists:permissions,id'],
            'created_at' => ['required'],
        ]);

        $consultation = new Consultation;

        // Check if consultation with the same parameters exists
        $exists = $consultation::query()
            ->where('case_id', $request['case_id'])
            ->where('user_id', $request['user_id'])
            ->where('permission_id', $request['permission_id'])
            ->where('created_at', Carbon::parse($request['created_at']))
            ->exists();

        if (! $exists) {
            $consultation->insert([
                'case_id' => $request['case_id'],
                'user_id' => $request['user_id'],
                'permission_id' => $request['permission_id'],
                'created_at' => $request['created_at'],
                'updated_at' => $request['created_at'],
            ]);
        }
    }

    public function get_consultations_number(Request $request): int
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'permission_id' => ['required', 'exists:permissions,id'],
        ]);

        $number = Company::query()
            ->where('id', $request['company_id'])
            ->first()
            ->permissions()
            ->where('permission_id', $request['permission_id'])
            ->first();

        return ($number) ? $number->getRelationValue('pivot')->number : 0;
    }

    public function get_used_consultation_number(Request $request): int
    {
        $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'permission_id' => ['required', 'exists:permissions,id'],
        ]);

        return Consultation::query()
            ->where('case_id', $request['case_id'])
            ->where('permission_id', $request['permission_id'])
            ->withTrashed()
            ->count();
    }

    public function set_delete_notification(Request $request): bool
    {
        $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
        ]);

        Cases::query()->where('id', $request['case_id'])->update(['eap_consultation_deleted' => true]);

        return true;
    }

    public function set_deleted_consultation(Request $request): int
    {
        $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'date' => ['required'],
            'time' => ['required'],
        ]);

        return Consultation::query()
            ->where([
                'case_id' => $request['case_id'],
                'created_at' => Carbon::parse($request['date'].' '.$request['time'])->format('Y-m-d H:i'),
            ])
            ->update([
                'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
    }

    public function create_wos_answers(Request $request, WosAnswers $wos_answers): void
    {
        $wos_array_keys = collect([1, 2, 3, 4, 5, 6]);
        $wos_failed = false;

        $wos_array_keys->each(function ($key) use ($request, &$wos_failed): void {
            if (! array_key_exists($key, $request->wos_answers) && ! array_key_exists((string) $key, $request->wos_answers)) {
                $wos_failed = true;
                Log::info("Could not create WOS answers for case {$request->case_id}, because not all answers were present.");

                return;
            }
        });

        if (! $wos_failed) {
            $wos_answers->create([
                'case_id' => $request->case_id,
                'country_id' => $request->country_id,
                'company_id' => $request->company_id,
                'answer_1' => $request->wos_answers[1],
                'answer_2' => $request->wos_answers[2],
                'answer_3' => $request->wos_answers[3],
                'answer_4' => $request->wos_answers[4],
                'answer_5' => $request->wos_answers[5],
                'answer_6' => $request->wos_answers[6],
            ]);
        }
    }

    public function get_next_online_appointment_booking(int $case_id)
    {
        try {
            $online_booking_data = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
            ])->get(config('app.eap_online_url').'/api/get_next_online_appointment_booking', [
                'case_id' => $case_id,
            ]);

            if ($online_booking_data->successful()) {
                $online_booking_data = json_decode($online_booking_data->body(), true);
            } else {
                Log::info("Failed to get online booking data for case({$case_id}): {$online_booking_data->body()}");

                $online_booking_data = [];
            }
        } catch (Exception $e) {
            Log::info("Failed to get online booking data for case({$case_id}): {$e->getMessage()}");

            $online_booking_data = [];
        }

        return $online_booking_data;
    }

    public function get_consultation_data(): array
    {
        request()->validate([
            'case_id' => ['required', 'exists:cases,id'],
        ]);

        $case = Cases::query()->where('id', request()->case_id)->first();

        if ($case) {
            $consultation_type = $case->values->where('case_input_id', 24)->first()->value;

            // Get the first consultation that is still upcoming compared to the current date and time
            $next_consultation = $case->consultations()
                ->orderByDesc('created_at')
                ->first();

            return [
                'date' => ($next_consultation) ? Carbon::parse($next_consultation->created_at)->format('Y-m-d H:i') : '',
                'type' => $consultation_type,
            ];
        }

        return [];
    }

    public function get_consultation_type(): array
    {
        request()->validate([
            'case_id' => ['required', 'exists:cases,id'],
        ]);

        $case = Cases::query()->where('id', request('case_id'))->first();
        if ($case) {
            return [
                'consultation_type' => Cases::query()->where('id', request('case_id'))->first()->values->where('case_input_id', 24)->first()->value,
            ];
        }

        return [];
    }

    public function send_case_assign_mail(): Response
    {
        request()->validate([
            'user_id' => ['required'],
            'case_id' => ['required'],
        ]);

        try {
            $user = User::query()->where('id', request('user_id'))->first();
            $case = Cases::query()->where('id', request('case_id'))->first();

            if ($user && $case) {
                Mail::to($user->email)->send(new AssignCaseMail($user, $case));
            }
        } catch (Exception $e) {
            Log::info('Failed to send case assigned mail to expert for case('.request('case_id')."): {$e->getMessage()}");
        }

        return response()->noContent();
    }

    public function change_consultation_type(): Response
    {
        request()->validate([
            'case_id' => ['required'],
            'consultation_type' => ['required', Rule::in([83, 82])], // Chat - 82, Video - 83
        ]);

        $case = Cases::query()->where('id', request('case_id'))->first();

        if ($case) {
            $case->values->where('case_input_id', 24)->first()->update(['value' => request('consultation_type')]); // 24 - consultation type case input
        }

        return response()->noContent();
    }

    public function get_case_confirmed_at_date(): array
    {
        request()->validate([
            'case_id' => ['required'],
        ]);

        $case = Cases::query()->where('id', request('case_id'))->first();

        return [
            'confirmed_at' => optional($case)->confirmed_at,
            'case_deleted' => ! (bool) $case,
        ];
    }

    public function send_compsych_survey(): void
    {
        request()->validate([
            'case_id' => ['required'],
        ]);

        $case = Cases::query()->find(request()->case_id);

        if ($case) {
            $compsych_survey_form_service = new CompsychSurveyService(CompsychSurveyType::CASE_CREATED);
            $compsych_survey_form_service->send_mail(
                $case->values->where('case_input_id', 4)->first()->value, // name of the client
                $case->values->where('case_input_id', 18)->first()->value,  // email
                $case->case_identifier
            );
        }
    }
}
