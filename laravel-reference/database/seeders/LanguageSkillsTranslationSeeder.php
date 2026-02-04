<?php

namespace Database\Seeders;

use App\Models\LanguageSkill;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class LanguageSkillsTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $language_skills = LanguageSkill::query()->get();

        $language_skills_hu = [
            1 => 'Cseh',
            2 => 'Szlovák',
            3 => 'Román',
            4 => 'Angol',
            5 => 'Lengyel',
            6 => 'Litván',
            7 => 'Szerb',
            8 => 'Bolgár',
            9 => 'Albán',
            10 => 'Olasz',
            11 => 'Német',
            12 => 'Francia',
            13 => 'Ukrán',
            14 => 'Horvát',
            15 => 'Magyar',
            16 => 'Észt',
            17 => 'Lett',
            18 => 'Litván',
            19 => 'Szlovén',
            20 => 'Orosz',
        ];

        $language_skills_en = [
            1 => 'Czech',
            2 => 'Slovakian',
            3 => 'Romanian',
            4 => 'English',
            5 => 'Polish',
            6 => 'Lithuanian',
            7 => 'Serbian',
            8 => 'Bulgarian',
            9 => 'Albanian',
            10 => 'Italian',
            11 => 'German',
            12 => 'French',
            13 => 'Ukranian',
            14 => 'Croatian',
            15 => 'Hungarian',
            16 => 'Estonian',
            17 => 'Latvian',
            18 => 'Lithuanian',
            19 => 'Slovenian',
            20 => 'Russian',
        ];

        // Hungarian translations
        foreach ($language_skills as $language) {
            Translation::query()->create([
                'value' => $language_skills_hu[$language->id],
                'language_id' => 1,
                'translatable_id' => $language->id,
                'translatable_type' => LanguageSkill::class,
            ]);
        }

        // English translations
        foreach ($language_skills as $language) {
            Translation::query()->create([
                'value' => $language_skills_en[$language->id],
                'language_id' => 3,
                'translatable_id' => $language->id,
                'translatable_type' => LanguageSkill::class,
            ]);
        }
    }
}
