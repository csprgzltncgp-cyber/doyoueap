<?php

namespace App\Exports;

use App\Exports\Sheets\RiportSheet;
use App\Exports\Sheets\RiportSummarizeSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class RiportSummarizeExport extends StringValueBinder implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected $data) {}

    public function sheets(): array
    {
        $sheets = [];

        $this->data->each(function (array $values) use (&$sheets): void {
            $sheets[] = new RiportSheet($values['country_id'], $values, false);
        });

        $cumulated_datas = [
            'cumulated' => [],
            'problem_type' => [],
            'is_crisis' => [],
            'problem_details' => [],
            'gender' => [],
            'employee_or_family_member' => [],
            'age' => [],
            'language' => [],
            'place_of_receipt' => [],
            'source' => [],
        ];

        foreach ($this->data as $value) {
            foreach (array_keys($cumulated_datas) as $key) {
                if (isset($value[$key])) {
                    $cumulated_datas[$key] = array_merge_recursive($cumulated_datas[$key], $value[$key]);
                }
            }
        }

        $sheets[] = new RiportSummarizeSheet($cumulated_datas);

        return $sheets;
    }
}
