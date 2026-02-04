<?php

namespace Database\Seeders;

use App\Models\Specialization;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class SpecializationTranslatableTypeNameChange extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Change translatable_type name
        $translatables = Translation::query()->where('translatable_type', 'App\Models\Specializations')->get();

        foreach ($translatables as $translatable) {
            $translatable->translatable_type = Specialization::class;
            $translatable->save();
        }
    }
}
