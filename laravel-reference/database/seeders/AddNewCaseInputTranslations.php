<?php

namespace Database\Seeders;

use App\Models\CaseInput;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class AddNewCaseInputTranslations extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hungarian translations
        Translation::query()->create([
            'value' => 'Nyelvtudás',
            'language_id' => 1,
            'translatable_id' => 65,
            'translatable_type' => CaseInput::class,
        ]);

        Translation::query()->create([
            'value' => 'Specializáció',
            'language_id' => 1,
            'translatable_id' => 66,
            'translatable_type' => CaseInput::class,
        ]);

        // English translations
        Translation::query()->create([
            'value' => 'Language skills',
            'language_id' => 3,
            'translatable_id' => 65,
            'translatable_type' => CaseInput::class,
        ]);

        Translation::query()->create([
            'value' => 'Specialization',
            'language_id' => 3,
            'translatable_id' => 66,
            'translatable_type' => CaseInput::class,
        ]);
    }
}
