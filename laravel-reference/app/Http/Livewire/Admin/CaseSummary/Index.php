<?php

namespace App\Http\Livewire\Admin\CaseSummary;

use App\Models\Cases;
use App\Models\CaseValues;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public $opened_year_months = [];

    public $search = '';

    public function render()
    {
        $all_year_months = [];
        $case_ids = [];

        $case_values = CaseValues::query()->where('case_input_id', 64)->get();

        foreach ($case_values as $case_value) {
            $case_creation_time = CaseValues::query()->where('case_id', $case_value->case_id)->where('case_input_id', 1)->first()->value;
            $year_month = substr(Carbon::parse($case_creation_time)->format('Y-m-d'), 0, -3);
            $all_year_months[] = $year_month;
            $case_ids[] = $case_value->case_id;
        }

        $filtered_year_months = array_unique($all_year_months);
        rsort($filtered_year_months);

        $cases = Cases::query()->whereIn('id', $case_ids)->with(['values'])
            ->when(! empty($this->search), function ($query): void {
                $query->where('case_identifier', 'like', "%{$this->search}%");
                $query->orWhereHas('values', function ($query): void {
                    $query->where('case_input_id', 64)->where('value', 'like', "%{$this->search}%");
                });
            })->get();

        return view('livewire.admin.case-summary.index', ['filtered_year_months' => $filtered_year_months, 'cases' => $cases]);
    }

    public function toggle($year_month): void
    {
        if (in_array($year_month, $this->opened_year_months)) {
            $this->opened_year_months = array_diff($this->opened_year_months, [$year_month]);
        } else {
            $this->opened_year_months[] = $year_month;
        }
    }

    public function resetSearch(): void
    {
        $this->search = '';
    }
}
