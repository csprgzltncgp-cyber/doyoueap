<?php

namespace Database\Seeders;

use App\Models\PrizeGame\Section;
use Illuminate\Database\Seeder;

class PrizegameRemoveSections extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove the first subtitle section from each existing prizegame
        Section::query()->where('type', 2)->get()->groupBy('content_id')->each(function ($sections): void {
            $sections->first()->delete();
        });

        // Remove the first lead tile section from each existing prizegame block 2
        Section::query()->where('type', 1)->where('block', 2)->get()->groupBy('content_id')->each(function ($sections): void {
            $sections->first()->delete();
        });

        // Remove the first lead tile section from each existing prizegame block 3
        Section::query()->where('type', 1)->where('block', 3)->get()->groupBy('content_id')->each(function ($sections): void {
            $sections->first()->delete();
        });
    }
}
