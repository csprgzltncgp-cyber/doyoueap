<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RiportSummarizeSheet implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(private $data) {}

    public function title(): string
    {
        return Str::title('Summarized Report');
    }

    public function view(): View
    {
        return view('excels.riport-summarize', [
            'cumulated_data' => $this->data,
        ]);
    }
}
