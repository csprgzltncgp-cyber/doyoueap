<?php

namespace App\Exports;

use App\Models\Cases;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ClosedCasesExport implements FromView, WithEvents, ShouldAutoSize
{
    public function __construct(protected $data) {}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                foreach (range('A', $event->sheet->getDelegate()->getHighestDataColumn()) as $column) {
                    $max = 0;

                    $i = 1;
                    foreach ($event->sheet->getDelegate()->getRowIterator() as $row) {
                        $value = $event->sheet->getDelegate()->getCell($column.$i);
                        $width = mb_strwidth($value);
                        if ($width > $max) {
                            $max = $width;
                        }
                        $i++;
                    }
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($max);
                }
            },
        ];
    }

    public function view(): View
    {
        $datas = Cases::getExportData($this->data);

        return view('excels.cases', ['data' => $datas]);
    }
}
