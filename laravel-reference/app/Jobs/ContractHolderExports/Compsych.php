<?php

namespace App\Jobs\ContractHolderExports;

use App\Exports\CustomRiport\CompsychExport;
use App\Models\Cases;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class Compsych implements ShouldQueue
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
        // Compsych id is 3
        $companies = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 3))->pluck('id');

        $new_cases = Cases::query()
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)
                    ->whereDate('value', '>=', Carbon::parse($this->from)->startOfDay())
                    ->whereDate('value', '<=', Carbon::parse($this->to)->endOfDay());
            })
            ->whereIn('company_id', $companies)
            ->get();

        $cases_with_new_consultations = Cases::query()
            ->whereHas('values', function ($query): void {
                $query->where('case_input_id', 1)
                    ->whereDate('value', '<=', Carbon::parse($this->from)->endOfDay());
            })
            ->whereHas('consultations', function ($query): void {
                $query->whereDate('created_at', '>=', Carbon::parse($this->from)->startOfDay())
                    ->whereDate('created_at', '<=', Carbon::parse($this->to)->endOfDay());
            })
            ->whereIn('company_id', $companies)
            ->get();

        $data = $new_cases->merge($cases_with_new_consultations)->map(fn ($case): array => [
            'case_identifier' => $case->case_identifier,
            'employee_or_family_member' => $case->values->where('case_input_id', 9)->first()->input_value->value,
            'company' => $case->company->name,
            'gender' => $case->values->where('case_input_id', 10)->first()->input_value->value,
            'age' => $case->values->where('case_input_id', 11)->first()->input_value->value,
            'city' => $case->case_location != null ? $case->case_location->getValue() : null,
            'country' => $case->country->name,
            'is_crisis' => $case->values->where('case_input_id', 3)->first()->input_value->value,
            'problem_details' => $case->values->where('case_input_id', 16)->first()->input_value->value,
            'consulting_type' => $case->values->where('case_input_id', 24)->first()->input_value->value,
            'source' => $case->values->where('case_input_id', 12)->first()->input_value->value,
            'problem_type' => $case->case_type != null ? $case->case_type->getValue() : null,
            'creation_date' => $case->values->where('case_input_id', 1)->first()->value,
            'number_of_consultations' => $case->consultations->count(),
            'dates_of_consultations' => $case->consultations->map(fn ($consultation) => Carbon::parse($consultation->created_at)->format('Y-m-d H:i'))->implode(' ,'),
        ]);

        Excel::store(new CompsychExport($data), '/contract-holder-exports/3/'.$this->from->year.'-'.$this->to->month.'.xlsx', 'private');
    }
}
