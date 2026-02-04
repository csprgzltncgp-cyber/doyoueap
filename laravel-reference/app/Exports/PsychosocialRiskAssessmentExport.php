<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PsychosocialRiskAssessmentExport implements FromView
{
    protected $headers;

    public function __construct(protected $users_data) {}

    public function view(): View
    {
        $user_data_rows = [];

        foreach ($this->users_data as $data) {
            $user_data = json_decode((string) $data->data, null, 512, JSON_THROW_ON_ERROR);
            foreach ($user_data as $answer_key => $value) {
                if (preg_match('/Q\d+-\d+/', (string) $answer_key)) {
                    $index = substr((string) $answer_key, 1, strpos((string) $answer_key, '-') - 1);
                    $user_data->sorted[$index][$answer_key] = $value;
                }
            }

            foreach ($user_data->sorted as $test_index => $test_value) {
                if ($test_index > 1) {
                    $sum = 0;
                    foreach ($user_data->sorted[$test_index] as $answer_value) {
                        $sum += $answer_value;
                    }

                    $sum /= (is_countable($user_data->sorted[$test_index]) ? count($user_data->sorted[$test_index]) : 0);

                    $user_data->sorted[$test_index]['sum'] = round($sum, 2);
                }
            }
            $user_data->Date = $data->Date;
            $user_data_rows[] = ['id' => $user_data->id, 'answers' => $user_data->sorted, 'date' => $user_data->Date];
        }

        return view('excels.psychosocial_risk_assessment', ['data' => $user_data_rows]);
    }
}
