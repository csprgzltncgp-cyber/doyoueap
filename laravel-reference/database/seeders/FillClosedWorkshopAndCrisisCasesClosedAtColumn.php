<?php

namespace Database\Seeders;

use App\Models\WorkshopCase;
use Illuminate\Database\Seeder;

class FillClosedWorkshopAndCrisisCasesClosedAtColumn extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Workshops
        $workshop_cases_to_update = WorkshopCase::query()->whereNotNull('number_of_participants')->get();

        foreach ($workshop_cases_to_update as $workshop_case) {
            $workshop_case->update([
                'closed_at' => now(),
            ]);
        }
    }
}
