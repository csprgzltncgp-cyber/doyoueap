<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapTranslation;
use App\Models\PrizeGame\Content;

class ContentTranslationController extends Controller
{
    public function index()
    {
        $contents = Content::query()
            ->with([
                'language',
                'company',
                'country',
            ])
            ->whereNotNull('language_id')
            ->whereNotNull('company_id')
            ->whereNotNull('country_id')
            ->get();

        return view('admin.prizegame.content-translations.index', ['contents' => $contents]);
    }

    public function show(Content $content)
    {
        $languages = EapLanguage::query()->get();
        $content->load([
            'sections',
            'sections.translations',
            'questions',
            'questions.translations',
            'questions.answers',
            'questions.answers.translations',
            'sections.documents',
            'sections.documents.translations',
        ]);

        return view('admin.prizegame.content-translations.show', ['content' => $content, 'languages' => $languages]);
    }

    public function store()
    {
        request()->validate([
            'translations' => ['required', 'array'],
            'model' => ['required', 'string'],
            'id' => ['required', 'string'],
        ]);

        foreach (request()->translations as $languageId => $translation) {
            EapTranslation::query()
                ->updateOrCreate([
                    'language_id' => $languageId,
                    'translatable_id' => request()->id,
                    'translatable_type' => request()->model,
                ], [
                    'value' => $translation ?? '',
                ]);
        }

        return redirect()->back()->with('success', __('common.case_input_edit.successful_save'));
    }
}
