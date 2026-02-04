<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapPrefix;
use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EapPrefixesController extends Controller
{
    public function index()
    {
        $prefixes = EapPrefix::all();
        $languages = EapLanguage::all();

        return view('admin.eap-online.prefixes.list', ['prefixes' => $prefixes, 'languages' => $languages]);
    }

    public function update(Request $request)
    {
        if ($new_prefixes = $request->get('new_prefixes')) {
            foreach ($new_prefixes as $new_prefix) {
                $prefix = EapPrefix::query()->create([
                    'name' => $new_prefix['name'],
                ]);
                $prefix->eap_prefix_translations()->save(new EapTranslation([
                    'language_id' => $new_prefix['language_id'],
                    'value' => $new_prefix['name'],
                ]));
            }
        }

        return redirect()->back();
    }

    public function delete($id): void
    {
        try {
            $prefix = EapPrefix::query()->findOrFail($id);

            foreach ($prefix->eap_prefix_translations()->get() as $translation) {
                $translation->delete();
            }

            $prefix->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    public function has_article_attached($id)
    {
        $prefix = EapPrefix::query()->find($id);

        return response(['has_article_attached' => $prefix->eap_articles->count() > 0]);
    }

    public function translate_view()
    {
        $prefixes = EapPrefix::all();
        $languages = EapLanguage::all();

        return view('admin.eap-online.prefixes.translate', ['prefixes' => $prefixes, 'languages' => $languages]);
    }

    public function translate_store(Request $request)
    {
        foreach ($request->get('prefixes') as $prefix) {
            foreach ($prefix['text'] as $language_id => $translation) {
                if ($prefix_translation = EapTranslation::query()->where(['translatable_type' => 'App\Models\Prefix', 'translatable_id' => $prefix['id'], 'language_id' => $language_id])->first()) {
                    $prefix_translation->value = $translation;
                    $prefix_translation->save();
                } elseif (! empty($translation)) {
                    $parent_prefix = EapPrefix::query()->find($prefix['id']);
                    $parent_prefix->eap_prefix_translations()->save(new EapTranslation([
                        'value' => $translation,
                        'language_id' => $language_id,
                    ]));
                }
            }
        }

        return redirect()->back();
    }
}
