<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EapCategoriesController extends Controller
{
    public function list_all_articles_type()
    {
        $type = 'all-articles';
        $categories = EapCategory::query()->where('type', $type)->get();
        $languages = EapLanguage::all();

        return view('admin.eap-online.categories.list.content', ['categories' => $categories, 'type' => $type, 'languages' => $languages]);
    }

    public function list_self_help_type()
    {
        $type = 'self-help';
        $parent_categories = EapCategory::query()->where('type', $type)->where('parent_id', null)->with('childs')->get();
        $categories = EapCategory::query()->where('type', $type)->get();
        $languages = EapLanguage::all();

        return view('admin.eap-online.categories.list.content', ['parent_categories' => $parent_categories, 'categories' => $categories, 'type' => $type, 'languages' => $languages]);
    }

    public function list_all_videos_type()
    {
        $type = 'all-videos';
        $categories = EapCategory::query()->where('type', $type)->get();
        $languages = EapLanguage::all();

        return view('admin.eap-online.categories.list.content', ['categories' => $categories, 'type' => $type, 'languages' => $languages]);
    }

    public function list_all_webinars_type()
    {
        $type = 'all-webinars';
        $categories = EapCategory::query()->where('type', $type)->get();
        $languages = EapLanguage::all();

        return view('admin.eap-online.categories.list.content', ['categories' => $categories, 'type' => $type, 'languages' => $languages]);
    }

    public function list_all_podcasts_type()
    {
        $type = 'all-podcasts';
        $categories = EapCategory::query()->where('type', $type)->get();
        $languages = EapLanguage::all();

        return view('admin.eap-online.categories.list.content', ['categories' => $categories, 'type' => $type, 'languages' => $languages]);
    }

    public function update(Request $request)
    {
        if ($categories_to_update = $request->get('old_categories')) {
            foreach ($categories_to_update as $category) {
                if (! array_key_exists('parent_id', $category)) {
                    EapCategory::query()->where('id', $category['category_id'])->update(['parent_id' => null]);
                } else {
                    EapCategory::query()->where('id', $category['category_id'])->update(['parent_id' => $category['parent_id'] ?? null]);
                }
            }
        }

        if ($new_categories = $request->get('new_categories')) {
            foreach ($new_categories as $category) {
                if (! array_key_exists('parent_id', $category)) {
                    $new_category = new EapCategory([
                        'parent_id' => null,
                        'name' => $category['name'],
                        'type' => $request->get('type'),
                    ]);
                } else {
                    $new_category = new EapCategory([
                        'parent_id' => $category['parent_id'],
                        'name' => $category['name'],
                        'type' => $request->get('type'),
                    ]);
                }

                $new_category->save();

                $new_category->eap_category_translations()->save(new EapTranslation([
                    'language_id' => $category['language_id'],
                    'value' => $category['name'],
                ]));
            }
        }

        return redirect()->back();
    }

    public function translate_view()
    {
        $categories = EapCategory::all();
        $languages = EapLanguage::all();
        $category_types = collect($categories->pluck('type', 'id'))->unique();

        return view('admin.eap-online.categories.translate', ['categories' => $categories, 'languages' => $languages, 'category_types' => $category_types]);
    }

    public function translate_store(Request $request)
    {
        foreach ($request->get('categories') as $id => $category) {
            foreach ($category['text'] as $language_id => $translation) {
                if ($category_translation = EapTranslation::query()->where(['translatable_type' => 'App\Models\Category', 'translatable_id' => $id, 'language_id' => $language_id])->first()) {
                    $category_translation->value = $translation;
                    $category_translation->save();
                } elseif (! empty($translation)) {
                    $parent_category = EapCategory::query()->find($id);
                    $parent_category->eap_category_translations()->save(new EapTranslation([
                        'value' => $translation,
                        'language_id' => $language_id,
                    ]));
                }
            }
        }

        return redirect()->back();
    }

    public function has_article_attached($id)
    {
        $category = EapCategory::query()->find($id);

        return response(['has_article_attached' => $category->eap_articles->count() > 0]);
    }

    public function delete($id)
    {
        try {
            $category = EapCategory::query()->findOrFail($id);

            foreach ($category->eap_category_translations as $translation) {
                $translation->delete();
            }

            $category->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.actions'));
    }
}
