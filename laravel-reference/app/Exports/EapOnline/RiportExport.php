<?php

namespace App\Exports\EapOnline;

use App\Traits\EapOnline\Riport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class RiportExport extends StringValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{
    use Riport;

    public function __construct(protected $data) {}

    public function view(): View
    {
        return view('excels.eap-online.riport', [
            'generated_riport' => $this->data,
        ]);
    }
}
