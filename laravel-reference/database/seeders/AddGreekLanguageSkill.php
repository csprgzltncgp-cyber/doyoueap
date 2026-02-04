<?php

namespace Database\Seeders;

use App\Models\LanguageSkill;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class AddGreekLanguageSkill extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add greek language skill
        $id = LanguageSkill::query()->create([
            'slug' => 'Görög',
        ])->id;

        // Hungarian translations
        Translation::query()->create([
            'value' => 'Görög',
            'language_id' => 1,
            'translatable_id' => $id,
            'translatable_type' => LanguageSkill::class,
        ]);

        // English translations
        Translation::query()->create([
            'value' => 'Greek',
            'language_id' => 3,
            'translatable_id' => $id,
            'translatable_type' => LanguageSkill::class,
        ]);
    }
}
