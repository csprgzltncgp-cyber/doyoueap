<?php

namespace App\Jobs\ContractHolderExports;

use App\Exports\CustomRiport\PulsoExport;
use App\Models\Cases;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class Pulso implements ShouldQueue
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
        // Pulso id is 5
        $companies = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 5))->pluck('id');

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
            'creation_date' => $case->values->where('case_input_id', 1)->first()->value,
            'case_identifier' => $case->case_identifier,
            'company' => $case->company->name,
            'problem_type' => $case->case_type != null ? $case->case_type->getValue() : null,
            'contact_type' => $case->values->where('case_input_id', 6)->first()->getValue(),
            'employee_or_family_member' => $case->values->where('case_input_id', 9)->first()->getValue(),
            'consulting_language' => $case->values->where('case_input_id', 32)->first()->getValue(),
            'gender' => $case->values->where('case_input_id', 10)->first()->getValue(),
            'age' => $case->values->where('case_input_id', 11)->first()->getValue(),
            'source' => $case->values->where('case_input_id', 12)->first()->getValue(),
            'consulting_type' => $case->values->where('case_input_id', 24)->first()->getValue(),
            'ages_in_company' => optional($case->values->where('case_input_id', 35)->first())->getValue(),
            'problem_details' => $case->values->where('case_input_id', 16)->first()->getValue(),
            'city' => $case->case_location != null ? $case->case_location->getValue() : null,
            'status' => optional($case->values->where('case_input_id', 55)->first())->getValue(),
            'case_status' => $this->get_pulso_status($case->getRawOriginal('status')),
            'function' => optional($case->values->where('case_input_id', 57)->first())->getValue(),
            'number_of_consultations' => $case->consultations->count(),
            'dates_of_consultations' => $case->consultations->map(fn ($consultation) => Carbon::parse($consultation->created_at)->format('Y-m-d H:i'))->implode(' ,'),
            'customer_satisfaction' => $case->customer_satisfaction,
        ]);

        Excel::store(new PulsoExport($data), '/contract-holder-exports/5/'.$this->from->year.'-'.$this->from->month.'.xlsx', 'private');
    }

    private function get_pulso_status($status): ?string
    {
        return match ($status) {
            'assigned_to_expert', 'employee_contacted', 'opened' => 'Ongoing',
            'confirmed', 'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed', 'client_unreachable' => 'Closed',
            default => null,
        };
    }
}
