<?php

namespace Database\Seeders;

use App\Enums\OtherActivityType;
use App\Models\OtherActivity;
use Illuminate\Database\Seeder;

class OtherActivityOrientationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $otherActivities = OtherActivity::query()->get();
        foreach ($otherActivities as $otherActivity) {
            $otherActivity->update([
                'type' => OtherActivityType::TYPE_ORIENTATION,
            ]);
        }
    }
}
