<?php

namespace App\Console\Commands;

use App\Jobs\TranslateTranslation;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Console\Command;

class CreateTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate translation to the selected language';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $target_lang = Language::query()->withoutGlobalScopes()->where('code', 'pl')->first();

        Translation::query()->where('language_id', 1)->get()->each(function (Translation $eng_translation) use ($target_lang): void {
            if (! Translation::query() // If translation does not exist in the target language.
                ->where('translatable_type', $eng_translation->translatable_type)
                ->where('translatable_id', $eng_translation->translatable_id)
                ->where('language_id', $target_lang->id)
                ->exists()) {
                TranslateTranslation::dispatch(
                    $eng_translation,
                    $target_lang, // to
                );
            }
        });
    }
}
