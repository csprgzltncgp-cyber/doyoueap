<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback\Language;
use App\Models\Feedback\LanguageLine;

class LanguageLinesController extends Controller
{
    public function index()
    {
        $languages = Language::query()->get();
        $translation_lines = LanguageLine::query()->get();

        return view('admin.feedback.language-lines.index', ['languages' => $languages, 'translation_lines' => $translation_lines]);
    }

    public function get_translation_lines()
    {
        $languages = Language::query()->get();

        if (empty(request()->input('needle'))) {
            $translation_lines = LanguageLine::query()->get();
        } else {
            $translation_lines = LanguageLine::query()->where('text', 'like', '%'.request()->input('needle').'%')->get();
        }

        return response()->json(['languages' => $languages, 'translation_lines' => $translation_lines]);
    }

    public function store()
    {
        $translations_to_update = request()->input('old');
        $translations_to_create = request()->input('new');

        if ($translations_to_create) {
            foreach ($translations_to_create as $line) {
                LanguageLine::query()->create([
                    'group' => 'system',
                    'key' => $line['key'],
                    'text' => array_filter($line['text']),
                ]);
            }
        }

        if ($translations_to_update) {
            foreach ($translations_to_update as $line) {
                $language_line = LanguageLine::query()->where('group', 'system')->where('key', $line['key'])->first();
                $language_line->text = array_filter($line['text']);
                $language_line->save();
            }
        }

        return redirect()->back();
    }
}
