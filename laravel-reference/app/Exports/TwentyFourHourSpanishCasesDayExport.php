<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class TwentyFourHourSpanishCasesDayExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly Collection $cases, private readonly string $date) {}

    public function title(): string
    {
        return Str::title(Carbon::parse($this->date)->locale('hu')->translatedFormat('l'));
    }

    public function view(): View
    {
        return view(
            'excels.24_hour_spanish_cases',
            [
                'cases' => $this->cases,
            ]
        );
    }
}
