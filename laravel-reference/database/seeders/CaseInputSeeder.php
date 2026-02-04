<?php

namespace Database\Seeders;

use App\Models\CaseInput;
use Illuminate\Database\Seeder;

class CaseInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CaseInput::query()->create([
            'id' => 65,
            'name' => 'Nyelvtudás',
            'default_type' => 'case_language_skill',
            'type' => 'select',
            'display_format' => 'icon',
            'chart' => 1,
            'delete_later' => 0,
        ]);

        CaseInput::query()->create([
            'id' => 66,
            'name' => 'Specializáció',
            'default_type' => 'case_specialization',
            'type' => 'select',
            'display_format' => 'icon',
            'chart' => 1,
            'delete_later' => 0,
        ]);
    }
}
