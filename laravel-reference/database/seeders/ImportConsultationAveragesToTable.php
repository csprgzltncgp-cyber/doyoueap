<?php

namespace Database\Seeders;

use App\Models\UsedConsultations;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImportConsultationAveragesToTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Storage::disk('private')->has('expert_consultations.json')) {
            $data = json_decode(Storage::disk('private')->get('expert_consultations.json'), true, 512, JSON_THROW_ON_ERROR);

            foreach ($data as $item) {
                if (isset($item['averages'])) {
                    if (isset($item['averages']['cgp'])) {
                        foreach ($item['averages']['cgp'] as $key => $value) {
                            $used_consultations = new UsedConsultations([
                                'cgp_employee' => true,
                                'type' => UsedConsultations::TYPE_AVERAGES,
                                'number_of_consultations' => $key,
                                'consultation_average' => $value,
                                'total_percentage' => '',
                            ]);
                            $used_consultations->save();
                        }
                    }

                    if (isset($item['averages']['affiliate'])) {
                        foreach ($item['averages']['affiliate'] as $key => $value) {
                            $used_consultations = new UsedConsultations([
                                'cgp_employee' => false,
                                'type' => UsedConsultations::TYPE_AVERAGES,
                                'number_of_consultations' => $key,
                                'consultation_average' => $value,
                                'total_percentage' => '',
                            ]);
                            $used_consultations->save();
                        }
                    }
                }

                if (isset($item['percentage_sum'])) {
                    if (isset($item['percentage_sum']['cgp'])) {
                        $used_consultations = new UsedConsultations([
                            'cgp_employee' => true,
                            'type' => UsedConsultations::TYPE_TOTAL_PERCENTAGE,
                            'number_of_consultations' => '',
                            'consultation_average' => '',
                            'total_percentage' => $item['percentage_sum']['cgp'],
                        ]);
                        $used_consultations->save();
                    }

                    if (isset($item['percentage_sum']['cgp'])) {
                        $used_consultations = new UsedConsultations([
                            'cgp_employee' => false,
                            'type' => UsedConsultations::TYPE_TOTAL_PERCENTAGE,
                            'number_of_consultations' => '',
                            'consultation_average' => '',
                            'total_percentage' => $item['percentage_sum']['affiliate'],
                        ]);
                        $used_consultations->save();
                    }
                }

            }
        }
    }
}
