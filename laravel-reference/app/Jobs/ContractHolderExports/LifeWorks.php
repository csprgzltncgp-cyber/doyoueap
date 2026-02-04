<?php

namespace App\Jobs\ContractHolderExports;

use App\Exports\CustomRiport\LifeWorksExport;
use App\Models\Cases;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LifeWorks implements ShouldQueue
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
        // LifeWorks id is 1
        $companies = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 1))->pluck('id');

        $in_progress_cases = Cases::query()
            ->whereIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)
                    ->whereDate('value', '>=', $this->from)
                    ->whereDate('value', '<=', $this->to);
            })
            ->whereIn('company_id', $companies)
            ->get();

        $closed_cases = Cases::query()
            ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)
                    ->whereDate('value', '>=', $this->from)
                    ->whereDate('value', '<=', $this->to);
            })
            ->whereBetween('confirmed_at', [Carbon::parse($this->from)->startOfDay(), Carbon::parse($this->to)->endOfDay()])
            ->whereIn('company_id', $companies)
            ->get();

        $data = $in_progress_cases->merge($closed_cases)->map(fn ($case): array => [
            'creation_date' => $case->values->where('case_input_id', 1)->first()->value,
            'case_identifier' => $case->case_identifier,
            'org_id' => $case->company->org_id ?? optional(DB::table('org_data')->where(['company_id' => $case->company_id, 'country_id' => $case->country_id])->first())->org_id,
            'location' => $case->country->name,
            'modality' => $this->get_lifeworks_consultation_type($case->values->where('case_input_id', 24)->first()->input_value),
            'client_category' => $this->get_lifeworks_client_category($case->values->where('case_input_id', 9)->first()->input_value),
            'gender' => $case->values->where('case_input_id', 10)->first()->getValue(),
            'year_of_birth' => $this->get_year_of_birth(optional($case->values->where('case_input_id', 11)->first())->input_value),
            'source_of_information' => $this->get_lifeworks_source($case->values->where('case_input_id', 12)->first()->input_value),
            'service' => $this->get_life_works_problem_type($case->case_type),
            'issue' => optional($this->get_lifeworks_problem_details($case->values->where('case_input_id', 16)->first()->input_value))['issue'],
            'sub-issue' => optional($this->get_lifeworks_problem_details($case->values->where('case_input_id', 16)->first()->input_value))['sub-issue'],
            'business_unit' => null,
            'years_of_service' => 'Declined',
            'job_band' => null,
            'hr_business' => optional($case->values->whereIn('case_input_id', [37, 40])->first())->getValue(),
            'referral_source' => optional($case->values->whereIn('case_input_id', [38, 41, 44])->first())->getValue(),
            'client_status' => optional($case->values->where('case_input_id', 39)->first())->getValue(),
            'sessions_provided' => $case->consultations->count(),
            'session_dates' => $case->consultations->map(fn ($consultation) => Carbon::parse($consultation->created_at)->format('Y-m-d'))->implode(' ,'),
            'case_status' => $this->get_lifeworks_status($case->getRawOriginal('status')),
            'notes' => null,
            'covid_19' => optional($case->values->where('case_input_id', 46)->first())->getValue(),
            'org_name' => $case->company->name,
            'consultations_count' => $case->consultations->whereBetween('created_at', [$this->from, $this->to])->count(), // Get consultation that happened in the riport month
        ]);

        Excel::store(new LifeWorksExport($data), '/contract-holder-exports/1/'.$this->from->year.'-'.$this->to->month.'.xlsx', 'private');
    }

    private function get_year_of_birth($case_value)
    {
        return match ($case_value->id) {
            11 => Carbon::parse('2005-01-01')->format('Y-m-d'),
            12 => Carbon::parse('2004-01-01')->format('Y-m-d'),
            13 => Carbon::parse('1994-01-01')->format('Y-m-d'),
            14 => Carbon::parse('1984-01-01')->format('Y-m-d'),
            15 => Carbon::parse('1974-01-01')->format('Y-m-d'),
            16 => Carbon::parse('1964-01-01')->format('Y-m-d'),
            default => 'Unknown',
        };
    }

    private function get_lifeworks_problem_details($case_value): ?array
    {
        return match ($case_value->id) {
            35 => [
                'issue' => 'Alcohol',
                'sub-issue' => 'Addiction_Related_Issue',
            ],
            36, 41 => [
                'issue' => 'Family',
                'sub-issue' => 'Communication',
            ],
            37 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Depression',
            ],
            38 => [
                'issue' => 'Addiction_Related_Issue',
                'sub-issue' => 'Smoking',
            ],
            39 => [
                'issue' => 'Addiction_Related_Issue',
                'sub-issue' => 'Drug',
            ],
            40 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Stress- Medical',
            ],
            42, 200, 203, 247 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Life Stages',
            ],
            43, 47 => [
                'issue' => 'Addiction Other',
                'sub-issue' => 'Addiction_Related_Issue',
            ],
            44, 74 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Real Estate',
            ],
            45, 55 => [
                'issue' => 'Work_Related_Issue',
                'sub-issue' => 'Workplace Stress',
            ],
            46 => [
                'issue' => 'Trauma_Issue',
                'sub-issue' => 'Violence - At Home',
            ],
            48 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Grief',
            ],
            49, 50 => [
                'issue' => 'Family',
                'sub-issue' => 'Parenting',
            ],
            51 => [
                'issue' => 'Family',
                'sub-issue' => 'Elder Related',
            ],
            52, 77, 183 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Debt/Credit',
            ],
            53, 54 => [
                'issue' => 'Work_Related_Issue',
                'sub-issue' => 'Work Relationships/Conflict',
            ],
            56, 57, 58 => [
                'issue' => 'Work_Related_Issue',
                'sub-issue' => 'Work Performance',
            ],
            59 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Suicidal Risk',
            ],
            60 => [
                'issue' => 'Couple_Relationship',
                'sub-issue' => 'Relationship - General',
            ],
            62 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Stress - Personal',
            ],
            63, 179 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Criminal Law',
            ],
            64, 201, 204 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Anxiety',
            ],
            65, 66 => [
                'issue' => 'Family',
                'sub-issue' => 'Child Behaviour',
            ],
            67 => [
                'issue' => 'Nutrition_Issue',
                'sub-issue' => 'Preventative Health',
            ],
            68 => [
                'issue' => 'Nutrition_Issue',
                'sub-issue' => 'General Healthy Eating',
            ],
            69, 72 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Child Custody',
            ],
            70 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Divorce',
            ],
            71 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Child Support',
            ],
            73, 182 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Will/Estates',
            ],
            75, 175 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Bankrupcy',
            ],
            76, 180 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Landlord-Tenant',
            ],
            78 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Investment Planning',
            ],
            168, 207 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Estate',
            ],
            169 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Insurance',
            ],
            170 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Investment Planning ',
            ],
            171 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Property law',
            ],
            172 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Debit/Credit',
            ],
            173 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Retirement',
            ],
            174, 209 => [
                'issue' => 'Financial_Issue',
                'sub-issue' => 'Taxes',
            ],
            176, 177, 178 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Property Law',
            ],
            181 => [
                'issue' => 'Legal_Issue',
                'sub-issue' => 'Property Law',
            ],
            202 => [
                'issue' => 'Nutrition_Issue',
                'sub-issue' => 'Sleeping Health',
            ],
            205 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Anger Issues',
            ],
            206 => [
                'issue' => 'Personal_Emotional_Issue',
                'sub-issue' => 'Self Esteem',
            ],
            265 => [
                'issue' => 'Other',
                'sub-issue' => 'Other',
            ],
            354 => [
                'issue' => 'Other',
                'sub-issue' => 'Health Related Problem',
            ],
            default => null,
        };
    }

    private function get_lifeworks_source($case_value): ?string
    {
        return match ($case_value->id) {
            18 => 'Workshop',
            19 => 'Other activity',
            20 => 'Supervisor/Manager',
            21 => 'Company Doctor',
            22 => 'Co-worker',
            23 => 'Dependent',
            24 => 'Promotional Literature',
            25 => 'Caller was a Previous Client',
            26 => 'Declined',
            default => null,
        };
    }

    private function get_lifeworks_status($status): ?string
    {
        return match ($status) {
            'assigned_to_expert', 'employee_contacted', 'opened' => 'Ongoing',
            'confirmed', 'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed', 'client_unreachable' => 'Closed',
            default => null,
        };
    }

    private function get_lifeworks_consultation_type($case_value): ?string
    {
        return match (optional($case_value)->id) {
            80 => 'In Person',
            81 => 'Telephonic',
            82 => 'E-Counseling',
            83 => 'Video-Counselling',
            default => null,
        };
    }

    private function get_lifeworks_client_category($case_value): string
    {
        if (optional($case_value)->id == 7) {
            return 'Employee';
        }

        return 'Dependent';
    }

    private function get_life_works_problem_type($problem_type): ?string
    {
        switch ($problem_type->value) {
            case 1: // Pszichológiai
                return 'Counselling';
            case 2: // Jogi
                return 'Legal';
            case 3: // Pénzügyi
                return 'Financial';
            default:
                $problem_type->getValue();
        }

        return null;
    }
}
