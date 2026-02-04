<?php

namespace App\Exports;

use App\Models\Cases;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TwentyFourHourAssignedCasesExport implements WithMultipleSheets
{
    public function __construct(private readonly Collection $cases) {}

    public function sheets(): array
    {
        // Group cases by the date it was first assigned to an expert
        $group = $this->cases->groupBy(fn (Cases $case) => $case->first_assigned_expert_by_date()->getRelationValue('pivot')->created_at->format('Y-m-d'));

        $sheets = collect([]);
        $group->each(function ($cases, $date) use (&$sheets): void {
            $sheets->push(new TwentyFourHourAssignedCasesDayExport($cases, $date));
        });

        return $sheets->toArray();
    }
}
