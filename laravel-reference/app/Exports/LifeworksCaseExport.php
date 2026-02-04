<?php

namespace App\Exports;

use App\Models\Cases;
use App\Models\CaseValues;
use App\Models\Company;
use App\Models\Country;
use App\Models\LanguageSkill;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Files\LocalTemporaryFile;

class LifeworksCaseExport implements WithEvents
{
    public function __construct(public Cases $case) {}

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $temp_file = new LocalTemporaryFile(storage_path('lifeworks-form.xlsx'));

                $event->writer->reopen($temp_file, Excel::XLSX);
                $sheet = $event->writer->getSheetByIndex(0);

                $this->populate_sheet($sheet);

                $event->writer->getSheetByIndex(0)->export($event->getConcernable());

                return $event->getWriter()->getSheetByIndex(0);
            },
        ];
    }

    private function populate_sheet($sheet): void
    {
        $current_locale = app()->getLocale();
        app()->setLocale('en');

        $phone = optional(CaseValues::query()->where('case_id', $this->case->id)->where('case_input_id', 17)->first())->value;
        $email = optional(CaseValues::query()->where('case_id', $this->case->id)->where('case_input_id', 18)->first())->value;

        $language_eng = '';

        if ($lang_input = CaseValues::query()->where('case_id', $this->case->id)->where('case_input_id', 32)->first()) {
            $language_eng = LanguageSkill::query()->where('id', $lang_input->value)->first()->translation->value; // Get language name in english
        }

        $sheet->setCellValue('B3', optional(CaseValues::query()->where('case_id', $this->case->id)->where('case_input_id', 4)->first())->value); // First Name
        $sheet->setCellValue('B4', '-'); // Last Name
        $sheet->setCellValue('B5', Carbon::parse(optional(Cases::query()->where('id', $this->case->id)->first())->created_at)->format('Y.m.d H:i')); // Date of Contact
        $sheet->setCellValue('B6', empty($phone) ? 'email' : 'phone'); // Preferred Method of Contact
        $sheet->setCellValue('B7', $phone); // Phone number
        $sheet->setCellValue('B8', $email); // E-mail Address
        $sheet->setCellValue('B9', Company::query()->find($this->case->company_id)->name); // Organization Name
        $sheet->setCellValue('B10', optional(Company::query()->find($this->case->company_id)->org_datas()->first())->org_id); // Org ID #
        $sheet->setCellValue('B11', Country::query()->find($this->case->country_id)->name); // Country (Work Location)
        $sheet->setCellValue('B12', $language_eng); // Language
        $sheet->setCellValue('B13', optional(CaseValues::query()->where('case_id', $this->case->id)->where('case_input_id', 16)->first())->getValue()); // Description of the Request

        app()->setLocale($current_locale);
    }
}
