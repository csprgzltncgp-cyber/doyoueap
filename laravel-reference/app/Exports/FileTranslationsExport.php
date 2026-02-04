<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FileTranslationsExport implements FromView, ShouldAutoSize
{
    public function __construct(private $translations, private $english_translations) {}

    public function view(): View
    {
        return view(
            'excels.file_translations',
            [
                'translations' => $this->translations,
                'english_translations' => $this->english_translations,
            ]
        );
    }
}
