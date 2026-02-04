<?php

namespace App\Jobs\ContractHolderExports;

use App\Exports\CustomRiport\OptumExport;
use App\Models\Cases;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class Optum implements ShouldQueue
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
    public function __construct(public $from, public $to) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Optum id is 4
        $companies = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 4))->pluck('id');

        $in_progress_cases = Cases::query()
            ->whereIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)
                    ->whereDate('value', '>=', Carbon::parse($this->from)->startOfDay())
                    ->whereDate('value', '<=', Carbon::parse($this->to)->endOfDay());
            })
            ->whereIn('company_id', $companies)
            ->get();

        $closed_cases = Cases::query()
            ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereBetween('confirmed_at', [Carbon::parse($this->from)->startOfDay(), Carbon::parse($this->to)->endOfDay()])
            ->whereIn('company_id', $companies)
            ->get();

        $data = $in_progress_cases->merge($closed_cases)->map(fn ($case): array => [
            'contract_organisation' => $case->company->name,
            'contract_report_id' => null,
            'reference_id' => $case->case_identifier,
            'optum_reference' => null,
            'country' => $case->country->name,
            'case_start_date' => $case->values->where('case_input_id', 1)->first()->value,
            'service_received' => $this->get_optum_problem_type(
                $case->case_type,
                $case->values->where('case_input_id', 24)->first()->input_value,
            ),
            'presenting_issue' => $this->get_optum_problem_details($case->values->where('case_input_id', 16)->first()->input_value),
            'level_of_functioning_at_case_opening' => null,
            'level_of_functioning_at_case_closure' => null,
            'level_of_stress_at_case_opening' => null,
            'level_of_stress_at_case_closure' => null,
            'days_absent_of_work' => null,
            'male_or_female' => $case->values->where('case_input_id', 10)->first()->getValue(),
            'information_source' => $this->get_optum_source($case->values->where('case_input_id', 12)->first()->input_value),
            'employee_type' => $this->get_optum_employee_type($case->values->where('case_input_id', 9)->first()->input_value),
            'age_rang' => $this->get_optum_ages($case->values->where('case_input_id', 11)->first()->input_value),
            'total_number_of_sessions' => $case->consultations->count(),
            'case_outcome' => $this->get_optum_status($case->getRawOriginal('status')),
            'date_case_closed' => $case->confirmed_at ? Carbon::parse($case->confirmed_at)->format('Y-m-d H:i') : null,
            'opening_score' => $case->phq9_opening,
            'closing_score' => $case->phq9_closing,
        ]);

        Excel::store(new OptumExport($data), '/contract-holder-exports/4/'.$this->from->year.'-'.$this->to->month.'.xlsx', 'private');
    }

    private function get_optum_problem_type($problem_type, $consultation_type): ?string
    {
        switch ($problem_type->value) {
            case 1:
                // Pszichológiai
                if ($consultation_type->id == 80) {
                    return 'Face to Face Counselling';
                }
                if ($consultation_type->id == 81) {
                    return 'Telephonic Counselling';
                }
                if ($consultation_type->id == 83) {
                    return 'Face to Face Counselling';
                }

                return 'Counselling';
                // no break
            case 2: // Jogi
                return 'Legal';
            case 3: // Pénzügyi
                return 'Financial';
            default:
                $problem_type->getValue();
        }

        return null;
    }

    private function get_optum_status($status): ?string
    {
        return match ($status) {
            'assigned_to_expert', 'employee_contacted', 'opened' => 'Case Still Open - Not Closed',
            'confirmed' => 'Problem Resolved',
            'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed', 'client_unreachable' => 'Client did not complete sessions',
            default => null,
        };
    }

    private function get_optum_source($case_value): ?string
    {
        return match ($case_value->id) {
            18 => 'Workshop',
            19 => 'Presentation/Training',
            20 => 'Supervisor / Manager',
            21 => 'Company Doctor',
            22 => 'Friend / Colleague',
            23 => 'Not Applicable (Family Member)',
            24 => 'Brochure/Poster',
            25 => 'Previously Seen / Used',
            26 => 'Declined',
            default => null,
        };
    }

    private function get_optum_employee_type($case_value)
    {
        if ($case_value->id == 8) {
            return 'Dependent';
        }

        return $case_value->translation->value;
    }

    private function get_optum_ages($case_value)
    {
        return match ($case_value->id) {
            17, 11 => $case_value->translation->value,
            13, 12 => '20 to 34',
            14 => '35 to 49',
            16, 15 => '50 to 65',
            default => null,
        };
    }

    private function get_optum_problem_details($case_value): ?string
    {
        return match ($case_value->id) {
            35 => 'Substance related - Alcohol',
            36, 60 => 'Family - Primary relationships',
            37 => 'Mood related - Depressed',
            38, 43, 47 => 'Substance related - Other/NOS',
            39 => 'Substance related - Drugs',
            40 => 'Health related - Other/NOS',
            41, 206 => 'Family - Other/NOS',
            42, 200 => 'Stress related - Life event/adjustment',
            44, 181, 207 => 'Legal - Other/NOS',
            45, 247 => 'Work related - Other/NOS',
            46, 454 => 'Family - Safeguarding concerns',
            48 => 'Family - Bereavement',
            49, 50 => 'Family - Parenting',
            51 => 'WorkLife - Elder care',
            52, 168 => 'Financial - Other/NOS',
            53, 54, 55 => 'Work related - Interpersonal conflict',
            56, 57 => 'Work related - Performance',
            58 => 'Work related - Work Stress',
            59 => 'Family - Difficult news/life event',
            62, 64, 201 => 'Stress related - Anxious/worry',
            63, 179 => 'Legal - Violation of local law',
            65 => 'Family - Parenting',
            66, 205 => 'Lifestyle related - Conflict resolution/anger management',
            67, 356 => 'Lifestyle related - Physical activity',
            68, 355 => 'Lifestyle related - Nutrition',
            69, 70, 71, 72 => 'Legal - Family',
            73, 182 => 'Legal - Estate planning/dispute',
            74, 76, 180 => 'Legal - Housing/Tenancy/Landlord',
            75, 77, 172, 175, 183 => 'Financial - Debt Management',
            78, 170 => 'Financial - Budget',
            169, 171 => 'Legal - Real Estate',
            173 => 'Work related - Retirement',
            174, 209 => 'Financial - Taxation',
            176, 178 => 'Legal - Civic/Local Issue',
            177 => 'Legal - Consumer',
            202 => 'Lifestyle related - Sleep',
            203, 204 => 'Stress related - Obsessive/compulsive',
            265 => 'Other',
            354 => 'Health Related - Other/NOS',
            default => null,
        };
    }
}
