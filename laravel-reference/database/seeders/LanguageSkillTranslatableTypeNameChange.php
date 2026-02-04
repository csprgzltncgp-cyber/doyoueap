<?php

namespace Database\Seeders;

use App\Models\LanguageSkill;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class LanguageSkillTranslatableTypeNameChange extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Change translatable_type name
        $translatables = Translation::query()->where('translatable_type', 'App\Models\LanguageSkills')->get();

        foreach ($translatables as $translatable) {
            $translatable->translatable_type = LanguageSkill::class;
            $translatable->save();
        }
    }
}
