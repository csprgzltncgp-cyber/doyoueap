<?php

namespace App\Exports\PrizeGame;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use function view;

class ResultsExport implements FromView
{
    public function __construct(public $winners) {}

    public function view(): View
    {
        return view('excels.prize_results', [
            'winners' => $this->winners,
        ]);
    }
}
