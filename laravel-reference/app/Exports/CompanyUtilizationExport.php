<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CompanyUtilizationExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private $data) {}

    public function view(): View
    {
        return view(
            'excels.company_utlization',
            [
                'companies' => $this->data,
            ]
        );
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }
}
