<?php

namespace App\Exports\CustomRiport;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class PulsoExport extends StringValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{
    public function __construct(protected $data) {}

    public function view(): View
    {
        return view('excels.custom-riport.pulso', ['data' => $this->data]);
    }
}
