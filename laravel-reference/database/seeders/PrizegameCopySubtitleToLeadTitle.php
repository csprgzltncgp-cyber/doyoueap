<?php

namespace Database\Seeders;

use App\Models\EapOnline\EapTranslation;
use App\Models\PrizeGame\Section;
use Illuminate\Database\Seeder;

class PrizegameCopySubtitleToLeadTitle extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lead_sections = Section::query()->where('type', 1)->where('block', 1)->get();

        $lead_sections->each(function (Section $lead_section): void {
            $sub_section = Section::query()
                ->where('type', 2)
                ->where('block', 1)
                ->where('content_id', $lead_section->content_id)
                ->first();

            if (! $sub_section) {
                return;
            }

            EapTranslation::query()
                ->where('translatable_type', Section::class)
                ->where('translatable_id', $sub_section->id)
                ->get()->each(function (EapTranslation $sub_translation) use ($lead_section): void {
                    EapTranslation::query()
                        ->where('translatable_type', Section::class)
                        ->where('translatable_id', $lead_section->id)
                        ->where('language_id', $sub_translation->language_id)
                        ->update([
                            'value' => $sub_translation->value,
                        ]);
                });
        });
    }
}
