<?php

namespace App\Exports\Asset;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AssetList implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings
{
    use Exportable;

    public function __construct(private $export) {}

    /**
     * @return Collection
     */
    public function collection()
    {
        return collect($this->export);
    }

    public function headings(): array
    {
        return [
            'Eszköz neve',
            'Saját azonosítoja',
            'CGP azonosítoja',
            'Akt. dátum',
            'Telefonszám',
            'PIN',
            'Szolgáltató',
            'Csomag',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
