<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\Assessment\EapQuestion as EapAssessmentQuestion;
use App\Models\EapOnline\Assessment\EapResult as EapAssessmentResult;
use App\Models\EapOnline\Assessment\EapType as EapAssessmentType;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapLanguageLines;
use App\Models\EapOnline\EapSetting;
use App\Models\EapOnline\EapTranslation;
use App\Models\EapOnline\WellBeing\EapQuestion as EapWellBeingQuestion;
use App\Models\EapOnline\WellBeing\EapResult as EapWellBeingResult;
use App\Models\EapOnline\WellBeing\EapType as EapWellBeingType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EapTranslationsController extends Controller
{
    public function system_view()
    {
        $languages = EapLanguage::all();
        $translation_lines = EapLanguageLines::query()->where('group', 'system')->get();

        return view('admin.eap-online.translations.system', ['languages' => $languages, 'translation_lines' => $translation_lines]);
    }

    public function get_translation_lines(Request $request)
    {
        $languages = EapLanguage::all();
        if (empty($request->get('needle'))) {
            $translation_lines = EapLanguageLines::query()->where('group', 'system')->get();
        } else {
            $translation_lines = EapLanguageLines::query()
                ->where('group', 'system')
                ->where(function ($query) use ($request): void {
                    $query->where('text->hu', 'LIKE', "%{$request->get('needle')}%")
                        ->orWhere('text->en', 'LIKE', "%{$request->get('needle')}%");
                })
                ->get();
        }

        return response()->json(['languages' => $languages, 'translation_lines' => $translation_lines]);
    }

    public function system_store(Request $request)
    {
        $translations_to_update = $request->get('old');
        $translation_to_create = $request->get('new');

        if ($translation_to_create) {
            foreach ($translation_to_create as $line) {
                EapLanguageLines::query()->create([
                    'group' => 'system',
                    'key' => $line['key'],
                    'text' => array_filter($line['text']),
                ]);
            }
        }

        if ($translations_to_update) {
            foreach ($translations_to_update as $line) {
                $translation_line = EapLanguageLines::query()->where('group', 'system')->where('key', $line['key'])->first();
                $translation_line->text = array_filter($line['text']);
                $translation_line->save();
            }
        }

        // Clear spatie translation loader cache
        try {
            $response = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
            ])->post(config('app.eap_online_url').'/api/translation-cache/clear');

            if (! $response->successful()) {
                Log::info("Failed to clear Spatie translation loader cache(EAP online): {$response->body()}");
            }
        } catch (Exception $e) {
            Log::info('Failed to clear Spatie translation loader cache(EAP online):'.$e->getMessage());
        }

        return redirect()->back();
    }

    public function assessment_view()
    {
        $languages = EapLanguage::all();
        $questions = EapAssessmentQuestion::with('eap_assessment_answers')->get();
        $results = EapAssessmentResult::all();
        $types = EapAssessmentType::all();

        return view('admin.eap-online.translations.assessment', ['languages' => $languages, 'questions' => $questions, 'results' => $results, 'types' => $types]);
    }

    public function well_being_view()
    {
        $languages = EapLanguage::all();
        $questions = EapWellBeingQuestion::with('eap_assessment_answers')->get();
        $results = EapWellBeingResult::all();
        $types = EapWellBeingType::all();

        return view('admin.eap-online.translations.assessment', ['languages' => $languages, 'questions' => $questions, 'results' => $results, 'types' => $types]);
    }

    public function well_being_store(Request $request)
    {
        $this->update_or_create_assessment_translation($request->get('questions'), 'App\Models\WellBeing\Question');
        $this->update_or_create_assessment_translation($request->get('answers'), 'App\Models\WellBeing\Answer');
        $this->update_or_create_assessment_translation($request->get('results'), 'App\Models\WellBeing\Result');
        $this->update_or_create_assessment_translation($request->get('types'), 'App\Models\WellBeing\Type');

        return redirect()->back();
    }

    public function assessment_store(Request $request)
    {
        $this->update_or_create_assessment_translation($request->get('questions'), 'App\Models\Assessment\Question');
        $this->update_or_create_assessment_translation($request->get('answers'), 'App\Models\Assessment\Answer');
        $this->update_or_create_assessment_translation($request->get('results'), 'App\Models\Assessment\Result');
        $this->update_or_create_assessment_translation($request->get('types'), 'App\Models\Assessment\Type');

        return redirect()->back();
    }

    private function update_or_create_assessment_translation($resource_array, string $resource_type): void
    {
        if (! empty($resource_array)) {
            foreach ($resource_array as $resource_id => $translations) {
                foreach ($translations as $language_id => $value) {
                    if (! empty($value)) {
                        EapTranslation::query()->updateOrCreate([
                            'translatable_id' => $resource_id,
                            'language_id' => $language_id,
                            'translatable_type' => $resource_type,
                        ], [
                            'value' => $value,
                        ]);
                    }
                }
            }
        }
    }

    public function theme_of_the_month_view()
    {
        $theme_of_the_month_language = EapSetting::query()->where('name', 'theme_of_the_month_language')->first();
        $languages = EapLanguage::all();

        return view('admin.eap-online.theme_of_the_month.translate', ['theme_of_the_month_language' => $theme_of_the_month_language, 'languages' => $languages]);
    }

    public function theme_of_the_month_store(Request $request)
    {
        foreach ($request->get('text') as $language_id => $translation) {
            if ($old_translation = EapTranslation::query()->where(['translatable_type' => 'App\Models\Setting', 'translatable_id' => $request->get('id'), 'language_id' => $language_id])->first()) {
                $old_translation->value = $translation;
                $old_translation->save();
            } elseif (! empty($translation)) {
                $parent = EapSetting::query()->find($request->get('id'));
                $parent->eap_translations()->save(new EapTranslation([
                    'value' => $translation,
                    'language_id' => $language_id,
                ]));
            }
        }

        return redirect()->back();
    }
}
