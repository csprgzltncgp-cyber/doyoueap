<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapLanguageLines;

class LanguageLinesController extends Controller
{
    public function index()
    {
        $languages = EapLanguage::query()->get();
        $translation_lines = EapLanguageLines::query()->where('group', 'system')->get();

        return view('admin.prizegame.language-lines.index', ['languages' => $languages, 'translation_lines' => $translation_lines]);
    }

    public function get_translation_lines()
    {
        $languages = EapLanguage::query()->get();

        if (empty(request()->input('needle'))) {
            $translation_lines = EapLanguageLines::query()->get();
        } else {
            $translation_lines = EapLanguageLines::query()->where('text', 'like', '%'.request()->input('needle').'%')->get();
        }

        return response()->json(['languages' => $languages, 'translation_lines' => $translation_lines]);
    }

    public function store()
    {
        $translations_to_update = request()->input('old');
        $translations_to_create = request()->input('new');

        if ($translations_to_create) {
            foreach ($translations_to_create as $line) {
                EapLanguageLines::query()->create([
                    'group' => 'system',
                    'key' => $line['key'],
                    'text' => array_filter($line['text']),
                ]);
            }
        }

        if ($translations_to_update) {
            foreach ($translations_to_update as $line) {
                $language_line = EapLanguageLines::query()->where('group', 'system')->where('key', $line['key'])->first();
                $language_line->text = array_filter($line['text']);
                $language_line->save();
            }
        }

        return redirect()->back();
    }
}
