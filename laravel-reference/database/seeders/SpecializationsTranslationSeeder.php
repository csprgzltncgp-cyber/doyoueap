<?php

namespace Database\Seeders;

use App\Models\Specialization;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class SpecializationsTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = Specialization::query()->get();

        $specializations_hu = [
            1 => 'Felnőtt terápia',
            2 => 'Pár terápia',
            3 => 'Gyerek terápia',
        ];

        $specializations_en = [
            1 => 'Adult therapy',
            2 => 'Couple therapy',
            3 => 'Child therapy',
        ];

        // Hungarian translations
        foreach ($specializations as $specialization) {
            Translation::query()->create([
                'value' => $specializations_hu[$specialization->id],
                'language_id' => 1,
                'translatable_id' => $specialization->id,
                'translatable_type' => Specialization::class,
            ]);
        }

        // English translations
        foreach ($specializations as $specialization) {
            Translation::query()->create([
                'value' => $specializations_en[$specialization->id],
                'language_id' => 3,
                'translatable_id' => $specialization->id,
                'translatable_type' => Specialization::class,
            ]);
        }
    }
}
