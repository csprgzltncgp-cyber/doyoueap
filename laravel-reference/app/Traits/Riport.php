<?php

namespace App\Traits;

use App\Models\CaseInputValue;
use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\EapRiport;
use App\Models\EapOnline\OnsiteConsultationPlace;
use App\Models\EapOnline\Statistics\EapLogin;
use App\Models\LanguageSkill;
use App\Models\Permission;
use App\Models\Riport as RiportModel;
use App\Models\RiportValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait Riport
{
    public $totalView = false;

    public function generate_quearter_riport_values($riports, $country, $quarter, $without_compared = false, $company = null, $year = null)
    {
        $company ??= auth()->user()->companies()->first();

        $riport_values = [];
        $problem_type_values = collect([]);
        foreach ($riports as $riport) {
            $current_problem_type_values = $riport->values()
                ->when(! $this->totalView, function ($query) use ($country): void {
                    $query->where('country_id', $country->id);
                })
                ->when($riport->to->format('Y-m-d') !== $riport->to->lastOfQuarter()->format('Y-m-d'), function ($query): void {
                    $query->where('is_ongoing', 0); // When not the last month of the quater, get only closed case data
                })
                ->where('type', RiportValue::TYPE_PROBLEM_TYPE)
                ->get();

            $problem_type_values = $problem_type_values->merge($current_problem_type_values);
            $generated_values = $this->generate_riport_values($country, $riport);

            if (array_key_exists($country->id, $riport_values)) {
                $riport_values[$country->id] = array_merge_recursive($riport_values[$country->id], $generated_values);
            } else {
                $riport_values[$country->id] = $generated_values;
            }
        }

        $riport_values = array_shift($riport_values);

        if ($without_compared) {
            return $riport_values;
        }

        // Regenerate gender compared to problem type
        $this->generate_numbers_compared_to_problem_type(RiportValue::TYPE_GENDER, 'gender', collect($problem_type_values), $riport_values);

        // Regenerate age according to problem type
        $this->generate_numbers_compared_to_problem_type(RiportValue::TYPE_AGE, 'age', collect($problem_type_values), $riport_values);

        // Regenerate records
        $this->generate_records($riport_values);

        // generate cumulated value by quarter
        $riport_values['cumulated'] = $this->generate_cumulated_value($country, $quarter, $company, $year);

        $riport_values['country_id'] = $country->id;

        // Generate onsite consultation site breakdown text
        $riport_values['onsite_consultation_site_breakdown_text'] = (data_get($riport_values, 'onsite_consultation_site')) ? $this->generate_onsite_consultation_site_text($riport_values['onsite_consultation_site']) : null;

        // Get eap login by quarter
        $riport_values['eap_logins'] = $this->get_eap_logins($company, $quarter, $country->id);

        return $riport_values;
    }

    // lezárt, kapcsolatt megszakad, nem elérhető, workshop, orientactio, egészségnap, szakértő kihelyezés, krizis, eap logins, nyereményjáték
    private function generate_cumulated_value($country, $quarter, $company, $year = null): array
    {
        $cumulated_values = [
            'text' => null,
            'closed' => 0,
            'ongoing' => 0,
            'interrupted' => 0,
            'client_unreachable' => 0,
            'workshop' => 0,
            'orientation' => 0,
            'health_day' => 0,
            'expert_outplacement' => 0,
            'crisis' => 0,
            'eap_logins' => 0,
            'prizegame' => 0,
            'consultations' => 0,
            'ongoing_consultations' => 0,
            'onsite_consultations' => 0,
            'all' => 0,
            'in_progress' => 0,
        ];

        // text
        $cumulated_values['text'] = implode('+', array_map(fn ($quarter): string => 'Q'.$quarter, range(1, $quarter)));

        foreach (range(1, (int) $quarter) as $quarter) {
            if (is_null($year)) {
                // check if today is in the first quarter of the current year
                if (Carbon::now()->quarter == 1) {
                    $from = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3 - 3);
                    $to = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3);
                } else {
                    $from = Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3);
                    $to = Carbon::now()->startOfYear()->addMonths($quarter * 3);
                }
            } else {
                $from = Carbon::now()->setYear($year)->startOfYear()->addMonths($quarter * 3 - 3);
                $to = Carbon::now()->setYear($year)->startOfYear()->addMonths($quarter * 3);
            }

            $riports = RiportModel::query()
                ->when(! $this->totalView, function ($query) use ($company): void {
                    $query->where('company_id', $company->id);
                })
                ->when($this->totalView, function ($query) use ($company): void {
                    $query->whereIn('company_id', $company->get_connected_companies()->pluck('id'));
                })
                ->where('is_active', true)
                ->where('from', '>=', $from)
                ->where('to', '<=', $to)
                ->get();

            if ($riports->count() === 0) {
                continue;
            }

            $values = $this->generate_quearter_riport_values($riports, $country, $quarter, true, $company);

            // closed
            $cumulated_values['closed'] += collect($values['case_numbers']['closed'])->sum();

            // interrupted
            $cumulated_values['interrupted'] += collect($values['case_numbers']['interrupted'])->sum();

            // clinet unreachable
            $cumulated_values['client_unreachable'] += collect($values['case_numbers']['client_unreachable'])->sum();

            // workshop
            $cumulated_values['workshop'] += collect($values['workshop']['participants_number'])->sum();

            // crisis
            $cumulated_values['crisis'] += collect($values['crisis']['participants_number'])->sum();

            // orientation activity
            $cumulated_values['orientation'] += collect($values['orientation']['participants_number'])->sum();

            // health day activity
            $cumulated_values['health_day'] += collect($values['health_day']['participants_number'])->sum();

            // expert outplacement activity
            $cumulated_values['expert_outplacement'] += collect($values['expert_outplacement']['participants_number'])->sum();

            // prizegame
            $cumulated_values['prizegame'] += collect($values['prizegame']['participants_number'])->sum();

            // consultations
            $cumulated_values['consultations'] += collect($values['consultations']['count'])->sum();

            // ongoing consultations
            $cumulated_values['ongoing_consultations'] += collect($values['ongoing_consultations']['count'])->sum();

            // onsite consultations
            $cumulated_values['onsite_consultations'] += collect($values['onsite_consultations']['count'])->sum();

            // onsite consultations
            $cumulated_values['in_progress'] += collect($values['case_numbers']['in_progress'])->last();

            if ($this->totalView) {
                $companies = $company->get_connected_companies();
                $eap_logins = 0;
                $c_from = $from->format('Y-m-d');
                $c_to = $to->subDay()->format('Y-m-d');
                foreach ($companies as $company) {
                    // get eap riports
                    $eap_riport = EapRiport::query()
                        ->where([
                            'is_active' => true,
                            'company_id' => $company->id,
                            'from' => $c_from,
                            'to' => $c_to,
                        ])->first();

                    // get eap logins for all company and country report values
                    if ($eap_riport) {
                        $eap_logins += optional($eap_riport->eap_riport_values()
                            ->where(['statistics' => EapLogin::class])
                            ->get())
                            ->sum('count');
                    }
                }
                $cumulated_values['eap_logins'] += $eap_logins;
            } else {
                // eap logins
                $eap_riport = EapRiport::query()
                    ->where([
                        'is_active' => true,
                        'company_id' => $company->id,
                        'from' => $from->format('Y-m-d'),
                        'to' => $to->subDay()->format('Y-m-d'),
                    ])->first();

                if ($eap_riport) {
                    $cumulated_values['eap_logins'] += optional($eap_riport->eap_riport_values()->where(['statistics' => EapLogin::class, 'country_id' => $country->id])->first())->current_count;
                }
            }
        }

        $cumulated_values['all'] += collect($cumulated_values)->except(['text', 'consultations', 'ongoing_consultations'])->sum();

        return $cumulated_values;
    }

    public function generate_riport_values($country, $riport, $only_case_numbers = false): array
    {
        $riport_values = [];

        $riport_values['case_numbers']['in_progress'] = get_in_progress_cases_count(company_id: $riport->company_id, country_id: $country->id, year: $riport->from->year, quarter: $riport->from->quarter);

        // Closed cases
        $riport_values['case_numbers']['closed'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_STATUS)
            ->where('value', 'confirmed')
            ->count();

        // All closed cases
        $riport_values['case_numbers']['cumulated'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['confirmed', 'interrupted', 'interrupted_confirmed', 'client_unreachable', 'client_unreachable_confirmed'])
            ->count();

        // Interrupted cases
        $riport_values['case_numbers']['interrupted'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['interrupted', 'interrupted_confirmed'])
            ->count();

        // Client unreachable cases
        $riport_values['case_numbers']['client_unreachable'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['client_unreachable', 'client_unreachable_confirmed'])
            ->count();

        // Sum of in progress and closed cases
        $riport_values['case_numbers']['partial_sum'] =
            $riport_values['case_numbers']['in_progress'] +
            $riport_values['case_numbers']['closed'];

        // Sum of case numbers
        $riport_values['case_numbers']['sum'] =
            $riport_values['case_numbers']['client_unreachable'] +
            $riport_values['case_numbers']['interrupted'] +
            $riport_values['case_numbers']['closed'] +
            $riport_values['case_numbers']['in_progress'];

        // Consultations
        $riport_values['consultations']['count'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_CONSULTATION_NUMBER)
            ->where('is_ongoing', false)
            ->sum('value');

        // Onsite consultations
        $riport_values['onsite_consultations']['count'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_ONSITE_CONSULTATION_STATUS)
            ->where('is_ongoing', false)
            ->where('value', 'booked')
            ->count();

        // Ongoing consultations (Only count values from the last month of the quarter)
        if ($riport->to->format('Y-m-d') === $riport->to->lastOfQuarter()->format('Y-m-d')) {
            $riport_values['ongoing_consultations']['count'] = $riport->values()
                ->when(! $this->totalView, function ($query) use ($country): void {
                    $query->where('country_id', $country->id);
                })
                ->where('type', RiportValue::TYPE_CONSULTATION_NUMBER)
                ->where('is_ongoing', true)
                ->sum('value');
        } else {
            $riport_values['ongoing_consultations']['count'] = 0;
        }

        // Workshop participants number
        $riport_values['workshop']['participants_number'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // All workshop participants number
        $riport_values['workshop']['all_participants_number'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })->where('type', RiportValue::TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // Other activity participants number
        $riport_values['orientation']['participants_number'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_ORIENTATION_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // All other activity participants number
        $riport_values['orientation']['all_participants_number'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })->where('type', RiportValue::TYPE_ORIENTATION_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // Health day participants number
        $riport_values['health_day']['participants_number'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // All health day participants number
        $riport_values['health_day']['all_participants_number'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })->where('type', RiportValue::TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // Expert outplacement participants number
        $riport_values['expert_outplacement']['participants_number'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // All expert outplacement participants number
        $riport_values['expert_outplacement']['all_participants_number'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })->where('type', RiportValue::TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // Prizegame participants number
        $riport_values['prizegame']['participants_number'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_PRIZEGAME_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // All prizegame participants number
        $riport_values['prizegame']['all_participants_number'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })->where('type', RiportValue::TYPE_PRIZEGAME_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // Crisis participants number
        $riport_values['crisis']['participants_number'] = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('type', RiportValue::TYPE_CRISIS_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // All crisis participants number
        $riport_values['crisis']['all_participants_number'] = RiportValue::query()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->whereHas('riport', function ($query) use ($riport): void {
                $query->where('company_id', $riport->company->id)->where('is_active', true);
            })->where('type', RiportValue::TYPE_CRISIS_NUMBER_OF_PARTICIPANTS)
            ->sum('value');

        // return only case numbers
        if ($only_case_numbers || collect($riport_values['case_numbers'])->sum() <= 0) {
            return $riport_values;
        }

        // If no cases recorded in current month, return only 0 for the first 4 boxes
        if (! $riport->values->when(! $this->totalView, function ($query) use ($country): void {
            $query->where('country_id', $country->id);
        })->count()) {
            return $riport_values;
        }

        // Problem type
        $problem_type_values = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->when($riport->to->format('Y-m-d') !== $riport->to->lastOfQuarter()->format('Y-m-d'), function ($query): void {
                $query->where('is_ongoing', 0); // When not the last month of the quater, get only closed case data
            })
            ->where('type', RiportValue::TYPE_PROBLEM_TYPE)
            ->get();

        $problem_type_total_count = $problem_type_values->count();
        $riport_values['problem_type'] = [];
        foreach ($problem_type_values->groupBy('value') as $permission_id => $values) {
            $permission = Permission::query()->where('id', $permission_id)->first()->translation->value;
            $riport_values['problem_type'][$permission] = [
                'total_count' => $problem_type_total_count,
                'count' => $values->count(),
                'id' => $permission_id,
            ];
        }

        if (empty($riport_values['problem_type'])) {
            unset($riport_values['problem_type']);
        }

        // Simple types
        $this->generate_simple_types_numbers([
            'problem_details' => RiportValue::TYPE_PROBLEM_DETAILS,
            'is_crisis' => RiportValue::TYPE_IS_CRISIS,
            'gender' => RiportValue::TYPE_GENDER,
            'employee_or_family_member' => RiportValue::TYPE_EMPLOYEE_OR_FAMILY_MEMBER,
            'age' => RiportValue::TYPE_AGE,
            'type_of_problem' => RiportValue::TYPE_TYPE_OF_PROBLEM,
            'place_of_receipt' => RiportValue::TYPE_PLACE_OF_RECEIPT,
            'source' => RiportValue::TYPE_SOURCE,
            'valeo_workplace_1' => RiportValue::TYPE_VALEO_WORKPLACE_1,
            'valeo_workplace_2' => RiportValue::TYPE_VALEO_WORKPLACE_2,
            'hydro_workplace' => RiportValue::TYPE_HYDRO_WORKPLACE,
            'pse_workplace' => RiportValue::TYPE_PSE_WORKPLACE,
            'michelin_workplace' => RiportValue::TYPE_MICHELIN_WORKPLACE,
            'sk_battery_workplace' => RiportValue::TYPE_SK_BATTERY_WORKPLACE,
            'grupa_workplace' => RiportValue::TYPE_GRUPA_WORKPLACE,
            'robert_bosch_workplace' => RiportValue::TYPE_ROBERT_BOSCH_WORKPLACE,
            'gsk_workplace' => RiportValue::TYPE_GSK_WORKPLACE,
            'johnson_and_johnson_workplace' => RiportValue::TYPE_JOHNSON_AND_JOHNSON_WORKPLACE,
            'syngenta_workplace' => RiportValue::TYPE_SYNGENTA_WORKPLACE,
            'nestle_workplace' => RiportValue::TYPE_NESTLE_WORKPLACE,
            'mahle_pl_workplace' => RiportValue::TYPE_MAHLE_PL_WORKPLACE,
            'lpp_workplace' => RiportValue::TYPE_LPP_WORKPLACE,
            'amrest_workplace' => RiportValue::TYPE_AMREST_WORKPLCAE,
            'kuka_workplace' => RiportValue::KUKA_WORKPLACE,
        ], $riport, $country, $riport_values);

        // Generate language data from language skills
        $this->generate_language_numbers('language', $riport, $country, $riport_values);

        // Gender compared to problem type
        $this->generate_numbers_compared_to_problem_type(RiportValue::TYPE_GENDER, 'gender', $problem_type_values, $riport_values);

        // Age according to problem type
        $this->generate_numbers_compared_to_problem_type(RiportValue::TYPE_AGE, 'age', $problem_type_values, $riport_values);

        // Generate records
        $this->generate_records($riport_values);

        // Generate onsite consultation site data
        $this->generate_onsite_consultation_site_numbers('onsite_consultation_site', $riport, $country, $riport_values);

        return $riport_values;
    }

    public function get_riport_interval($quarter = null, $year = null): array
    {
        if (! is_null($year)) {
            return [
                'from' => Carbon::now()->setYear($year)->startOfYear()->addMonths($quarter * 3 - 3),
                'to' => Carbon::now()->setYear($year)->startOfYear()->addMonths($quarter * 3),
            ];
        }

        // check if today is in the first quarter of the current year
        if (Carbon::now()->quarter == 1) {
            return [
                'from' => Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3 - 3),
                'to' => Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3),
            ];
        }

        return [
            'from' => Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3),
            'to' => Carbon::now()->startOfYear()->addMonths($quarter * 3),
        ];
    }

    private function generate_simple_types_numbers($simple_types, $riport, $country, array &$riport_values): void
    {
        foreach ($simple_types as $category_name => $type) {
            $case_input_values = $riport->values()
                ->when(! $this->totalView, function ($query) use ($country): void {
                    $query->where('country_id', $country->id);
                })
                ->when($riport->to->format('Y-m-d') !== $riport->to->lastOfQuarter()->format('Y-m-d'), function ($query): void {
                    $query->where('is_ongoing', 0); // When not the last month of the quater, get only closed case data
                })
                ->where('type', $type)
                ->get();

            $total_count = $case_input_values->count();

            $riport_values[$category_name] = [];

            foreach ($case_input_values->sortBy('value')->groupBy('value') as $case_input_value_id => $values) {
                $case_input_value = CaseInputValue::withTrashed()->where('id', $case_input_value_id)->first();
                $riport_values[$category_name][$case_input_value->translation->value] = [
                    'total_count' => $total_count,
                    'count' => $values->count(),
                    'id' => $case_input_value_id,
                ];

                if ($type == RiportValue::TYPE_PROBLEM_DETAILS) {
                    $permission = Permission::query()->where('id', $case_input_value->permission_id)->first();
                    if (! array_key_exists('permission', $riport_values[$category_name][$case_input_value->translation->value]) && $permission) {
                        $riport_values[$category_name][$case_input_value->translation->value]['permission'] = $permission->translation->value;
                    }
                }
            }

            if (empty($riport_values[$category_name])) {
                unset($riport_values[$category_name]);
            }
        }
    }

    private function generate_onsite_consultation_site_numbers($category, $riport, $country, array &$riport_values): void
    {
        $onsite_consultatin_site_values = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->where('is_ongoing', 0)
            ->where('type', RiportValue::TYPE_ONSITE_CONSULTATION_SITE)
            ->get();

        $total_count = $onsite_consultatin_site_values->count();

        foreach ($onsite_consultatin_site_values->sortBy('value')->groupBy('value') as $input_value_id => $values) {
            /** @var OnsiteConsultationPlace|null $place */
            $place = OnsiteConsultationPlace::query()->find((int) $input_value_id);

            if (! $place) {
                continue;
            }

            $riport_values[$category][$place->name] = [
                'total_count' => $total_count,
                'count' => $values->count(),
                'id' => $input_value_id,
            ];
        }
    }

    private function generate_onsite_consultation_site_text(array $onsite_place_data): string
    {
        $text = '';

        return $text.(collect($onsite_place_data)
            ->map(fn ($data, $place): string => $place.': '.collect($data['count'])->sum())
            ->implode("\n")."\n");
    }

    private function generate_language_numbers($category, $riport, $country, array &$riport_values): void
    {
        $language_input_values = $riport->values()
            ->when(! $this->totalView, function ($query) use ($country): void {
                $query->where('country_id', $country->id);
            })
            ->when($riport->to->format('Y-m-d') !== $riport->to->lastOfQuarter()->format('Y-m-d'), function ($query): void {
                $query->where('is_ongoing', 0); // When not the last month of the quater, get only closed case data
            })
            ->where('type', 32)
            ->get();
        $total_count = $language_input_values->count();

        foreach ($language_input_values->sortBy('value')->groupBy('value') as $input_value_id => $values) {
            $value = optional(LanguageSkill::query()->where('id', $input_value_id)->first()->translation)->value;

            if (! $value) {
                continue;
            }

            $riport_values[$category][$value] = [
                'total_count' => $total_count,
                'count' => $values->count(),
                'id' => $input_value_id,
            ];
        }
    }

    private function generate_numbers_compared_to_problem_type($type, string $name, $problem_type_values, array &$riport_values): void
    {
        $type_x_problem_type = [];

        foreach ($problem_type_values->groupBy('value') as $permission_id => $values) {
            $permission_count = $values->count();
            $permission = Permission::query()->where('id', $permission_id)->first()->translation->value;

            $type_x_problem_type[$permission] = [];
            foreach ($values as $value) {
                $connected_type = RiportValue::query()
                    ->where('connection_id', $value->connection_id)
                    ->where('type', $type)
                    ->first();
                if ($connected_type) {
                    $case_input_value = CaseInputValue::withTrashed()->where('id', $connected_type->value)->first();

                    if (array_key_exists($case_input_value->translation->value, $type_x_problem_type[$permission])) {
                        $type_x_problem_type[$permission][$case_input_value->translation->value]['count']++;
                    } else {
                        $type_x_problem_type[$permission][$case_input_value->translation->value] = [
                            'case_input_value_id' => $case_input_value->id,
                            'total_count' => $permission_count,
                            'count' => 1,
                        ];
                    }
                }
            }

            // fill blank values
            $case_input_values = CaseInputValue::withTrashed()->where('case_input_id', $type)->get();

            foreach ($case_input_values as $case_input_value) {
                $case_input_value_translation = $case_input_value->translation->value;
                if (! array_key_exists($case_input_value_translation, $type_x_problem_type[$permission])) {
                    $type_x_problem_type[$permission][$case_input_value_translation] = [
                        'case_input_value_id' => $case_input_value->id,
                        'total_count' => $permission_count,
                        'count' => 0,
                    ];
                }
            }
        }

        // sort results
        foreach ($type_x_problem_type as $key => $values) {
            $type_x_problem_type[$key] = collect($values)->sortBy(fn ($value) => $value['case_input_value_id']);
        }

        $riport_values[$name.'_x_problem_type'] = collect($type_x_problem_type)->sortBy('case_input_value_id');
    }

    private function generate_records(array &$riport_values): void
    {
        if (! array_key_exists('problem_type', $riport_values)) {
            return;
        }

        if (! array_key_exists('gender', $riport_values) || empty($riport_values['gender'])) {
            return;
        }

        if (! array_key_exists('age', $riport_values) || empty($riport_values['age'])) {
            return;
        }

        foreach ($riport_values['problem_type'] as $key => $value) {
            $riport_values['problem_type'][$key]['count'] = collect($riport_values['problem_type'][$key]['count'])->sum();
        }

        foreach ($riport_values['gender'] as $key => $value) {
            $riport_values['gender'][$key]['count'] = collect($riport_values['gender'][$key]['count'])->sum();
        }

        foreach ($riport_values['age'] as $key => $value) {
            $riport_values['age'][$key]['count'] = collect($riport_values['age'][$key]['count'])->sum();
        }

        // Record of the month: problem type
        $riport_values['record']['problem_type'] = collect(
            $riport_values['problem_type']
        )->where(
            'count',
            collect(
                $riport_values['problem_type']
            )->max('count')
        )->map(fn ($value) => Permission::query()->where('id', $value['id'])->first())->pop();

        // Record of the month: gender
        $riport_values['record']['gender'] = collect(
            $riport_values['gender_x_problem_type'][$riport_values['record']['problem_type']->translation->value]
        )->where(
            'count',
            collect(
                $riport_values['gender_x_problem_type'][$riport_values['record']['problem_type']->translation->value]
            )->max('count')
        )->map(fn ($value) => CaseInputValue::query()->where('id', $value['case_input_value_id'])->first())->pop();

        // Record of the month: age
        $riport_values['record']['age'] = collect(
            $riport_values['age_x_problem_type'][$riport_values['record']['problem_type']->translation->value]
        )->where(
            'count',
            collect(
                $riport_values['age_x_problem_type'][$riport_values['record']['problem_type']->translation->value]
            )->max('count')
        )->map(fn ($value) => CaseInputValue::query()->where('id', $value['case_input_value_id'])->first())->pop();
    }

    private function get_riport_data($quarter = null, ?Country $country = null, $year = null, $totalView = false): ?array
    {

        $user = auth()->user();
        $company = $user->companies()->first();
        $current_country = $country ?? $company->countries->sortBy('name')->first();
        $connected_companies = $company->get_connected_companies();

        if (empty($quarter)) {
            $quarter = get_last_quarter();
        }

        if ($totalView) {
            $this->totalView = true;
        }

        $current_interval = $this->get_riport_interval($quarter, $year);
        $from = $current_interval['from'];
        $to = $current_interval['to'];

        $riports = RiportModel::query()
            ->when(! $this->totalView, function ($query) use ($company): void {
                $query->where([
                    'company_id' => $company->id,
                ]);
            })
            ->when($this->totalView, function ($query) use ($connected_companies): void {
                $query->whereIn('company_id', $connected_companies->pluck('id'));
            })
            ->where('is_active', true)
            ->where('from', '>=', $from)
            ->where('to', '<=', $to)
            ->with('values')->get();

        if ($riports->count() === 0) {
            return null;
        }

        // When company is Superbet(717), need to hide all countries name except Romania, Poland and Croatia (6, 2, 12)
        if ($company->id == 717) {
            $countries = $company->countries->map(function ($country, string $index) {
                if (! in_array($country->id, [2, 6, 12])) {
                    $country->name = __('common.country').' '.$index;
                }

                return $country;
            });
        } else {
            $countries = $company->countries;
        }

        return [
            'countries' => $countries->sortBy('email'),
            'connected_companies' => $connected_companies->sortBy('name'),
            'current_country' => $current_country,
            'values' => $this->generate_quearter_riport_values($riports, $current_country, $quarter, false, $company, $year),
            'quarter' => $quarter,
            'to' => $to,
            'from' => $from,
        ];
    }

    private function get_eap_logins(Company $company, int $quarter, int $country_id): int
    {
        if (Carbon::now()->quarter == 1) {
            $from = Carbon::now()->subYear()->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->subYear()->startOfYear()->addMonths($quarter * 3);
        } else {
            $from = Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->startOfYear()->addMonths($quarter * 3);
        }

        $eap_riport = EapRiport::query()
            ->where([
                'is_active' => true,
                'company_id' => $company->id,
                'from' => $from->format('Y-m-d'),
                'to' => $to->subDay()->format('Y-m-d'),
            ])->first();

        if ($eap_riport) {
            return optional($eap_riport->eap_riport_values()
                ->where(['statistics' => EapLogin::class])
                ->where('country_id', $country_id)
                ->get())
                ->sum('count');
        }

        return 0;
    }

    private function get_cached_riport_data($quarter = null, ?Country $country = null, $year = null, $totalView = false)
    {
        $company = $company = auth()->user()->companies()->first();
        $country = $country ?: auth()->user()->country;

        if ($totalView) {
            return Cache::remember(
                'riport-'.$company->id.'-'.$quarter.'-total',
                60 * 60 * 24 * 30,
                fn (): ?array => $this->get_riport_data($quarter, $country, $year, $totalView)
            );
        }

        return Cache::remember(
            'riport-'.$quarter.'-'.optional($country)->id.'-'.$company->id,
            60 * 60 * 24 * 30,
            fn (): ?array => $this->get_riport_data($quarter, $country, $year, $totalView)
        );
    }
}
