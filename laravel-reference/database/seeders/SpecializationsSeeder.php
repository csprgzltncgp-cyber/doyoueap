<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            1 => 'Felnőtt terápia',
            2 => 'Pár terápia',
            3 => 'Gyerek terápia',
        ];

        foreach ($specializations as $id => $slug) {
            Specialization::query()->create([
                'id' => $id,
                'slug' => $slug,
            ]);
        }
    }
}
