<?php

namespace App\Exports;

use App\Models\Cases;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TwentyFourHourSpanishCasesExport implements WithMultipleSheets
{
    public function __construct(private readonly Collection $cases) {}

    public function sheets(): array
    {
        // Group cases by the date the case was created at
        $group = $this->cases->groupBy(fn (Cases $case) => $case->created_at->format('Y-m-d'));

        $sheets = collect([]);
        $group->each(function ($cases, $date) use (&$sheets): void {
            $sheets->push(new TwentyFourHourSpanishCasesDayExport($cases, $date));
        });

        return $sheets->toArray();
    }
}
