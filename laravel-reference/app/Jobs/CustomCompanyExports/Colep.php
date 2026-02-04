<?php

namespace App\Jobs\CustomCompanyExports;

use App\Exports\CustomCompany\Colep as ColepExport;
use App\Models\Cases;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class Colep implements ShouldQueue
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
        // colep id is 613
        $company = Company::query()->where('id', 613)->first();

        $cases = Cases::query()
            ->whereBetween('created_at', [Carbon::parse($this->from)->startOfDay(), Carbon::parse($this->to)->endOfDay()])
            ->where('company_id', $company->id)
            ->get();

        $data = $cases->map(fn ($case): array => [
            'date_of_intake' => $case->values->where('case_input_id', 1)->first()->value,
            'company_name' => 'Colep',
            'language' => $case->values->where('case_input_id', 32)->first()->input_value->value,
            'type_of_consuelling' => $case->values->where('case_input_id', 24)->first()->input_value->value,
            'type_of_issue' => $case->case_type != null ? $case->case_type->getValue() : null,
            'employee_type' => $case->values->where('case_input_id', 9)->first()->input_value->value,
            'gender' => $case->values->where('case_input_id', 10)->first()->input_value->value,
            'age' => $case->values->where('case_input_id', 11)->first()->input_value->value,
            'refferral_source' => $case->values->where('case_input_id', 12)->first()->input_value->value,
            'issue' => $case->values->where('case_input_id', 16)->first()->input_value->value,
        ]);

        Excel::store(new ColepExport($data), '/custom-company-exports/'.$company->id.'/'.$this->from->year.'-'.$this->to->month.'.xlsx', 'private');
    }
}
