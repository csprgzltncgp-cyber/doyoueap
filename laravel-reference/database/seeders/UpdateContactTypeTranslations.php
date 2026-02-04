<?php

namespace Database\Seeders;

use App\Models\CaseInputValue;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class UpdateContactTypeTranslations extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Email
        Translation::query()
            // ->where('language_id', 1)
            ->where('translatable_id', 82)
            ->where('translatable_type', CaseInputValue::class)
            ->update([
                'value' => 'Chat',
            ]);

        // Video
        Translation::query()
            ->where('translatable_id', 83)
            ->where('translatable_type', CaseInputValue::class)
            ->update([
                'value' => 'Video',
            ]);
    }
}
