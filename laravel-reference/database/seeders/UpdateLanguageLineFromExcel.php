<?php

namespace Database\Seeders;

use App\Models\EapOnline\EapLanguageLines;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class UpdateLanguageLineFromExcel extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/eap_translations-1.xlsx');

        $data = Excel::toArray([], $path);

        $language_lines = [];
        foreach ($data as $rows) {
            foreach ($rows as $key => $row) {
                if ($key != 0) {
                    $language_lines[] = [
                        'key' => $row[0],
                        'lang' => 'sr',
                        'translation' => $row[2],
                    ];
                    $language_lines[] = [
                        'key' => $row[0],
                        'lang' => 'ro',
                        'translation' => $row[3],
                    ];
                    $language_lines[] = [
                        'key' => $row[0],
                        'lang' => 'cz',
                        'translation' => $row[4],
                    ];
                    $language_lines[] = [
                        'key' => $row[0],
                        'lang' => 'sk',
                        'translation' => $row[5],
                    ];
                    $language_lines[] = [
                        'key' => $row[0],
                        'lang' => 'pl',
                        'translation' => $row[6],
                    ];
                }
            }

        }

        foreach ($language_lines as $updated_line) {
            $current_line = EapLanguageLines::query()->where('key', $updated_line['key'])->first();
            $text = $current_line->text;
            $text[$updated_line['lang']] = $updated_line['translation'];
            $current_line->text = $text;
            $current_line->save();
        }
    }
}
