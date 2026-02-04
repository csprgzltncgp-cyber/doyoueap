<?php

namespace App\Exports\CustomCompany;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class Colep extends StringValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{
    public function __construct(protected $data) {}

    public function view(): View
    {
        return view('excels.custom-company.colep', ['data' => $this->data]);
    }
}
