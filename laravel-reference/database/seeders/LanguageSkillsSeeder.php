<?php

namespace Database\Seeders;

use App\Models\LanguageSkill;
use Illuminate\Database\Seeder;

class LanguageSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
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

        foreach ($languages as $id => $slug) {
            LanguageSkill::query()->create([
                'id' => $id,
                'slug' => $slug,
            ]);
        }
    }
}
