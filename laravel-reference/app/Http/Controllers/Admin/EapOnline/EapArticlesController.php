<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapArticle;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapChapter;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapLesson;
use App\Models\EapOnline\EapSection;
use App\Models\EapOnline\EapSectionAttachment;
use App\Models\EapOnline\EapTranslation;
use App\Traits\EapOnline\SlugifyTrait;
use App\Traits\EapOnline\ThumbnailTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EapArticlesController extends Controller
{
    use SlugifyTrait;
    use ThumbnailTrait;

    public function index()
    {
        return view('admin.eap-online.articles.list');
    }

    public function get_articles(Request $request)
    {
        $articles = EapArticle::query()
            ->with('eap_visibility', 'eap_sections', 'eap_sections.eap_section_translations')
            ->when(! empty($request->get('needle')), function ($query) use ($request): void {
                $needle = urldecode((string) $request->get('needle'));
                $query->whereHas('eap_sections', function ($query2) use ($needle): void {
                    $query2->where('type', 'headline')->whereHas('eap_section_translations', function ($query3) use ($needle): void {
                        $query3->where(function ($query4): void {
                            $query4->where('language_id', 3)->orWhere('language_id', 1);
                        })->where('value', 'like', '%'.$needle.'%');
                    });
                });
            })
            ->paginate(5);

        return View::make('admin.eap-online.articles.list-inner', ['articles' => $articles, 'translation' => $request->get('translation')])->render();
    }

    public function store(Request $request)
    {
        $type = $request->get('type');
        $visibility = $request->get('visibility');
        $categories = $request->get('categories');

        $validator = Validator::make($request->all(), [
            'headline' => ['required', 'string'],
            'lead' => ['required'],
            'language' => ['required'],
            'thumbnail-preview' => ['required', 'max:5120'],
            'sections.*.content' => ['max:5120'],
        ]);

        if (! empty($visibility)) {
            $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

            $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
        }

        $validator->sometimes('prefix', 'required', fn (): bool => $type == 'rovat');

        $validator->sometimes('type', 'required', fn (): bool => $visibility == 'theme_of_the_month' || $visibility == 'home_page');

        if (! in_array($visibility, ['burnout_page', 'domestic_violence_page'])) {
            $validator->sometimes('categories', 'required', function () use ($categories): bool {
                $all_articles_categories = EapCategory::query()->where('type', 'all-articles')->pluck('id')->toArray();

                return count(array_intersect($all_articles_categories, $categories ?? [])) == 0;
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $slug = $this->slugify($request->get('headline'), 'article');

        $article = EapArticle::query()->create([
            'slug' => $slug,
            'type' => $request->get('type'),
            'input_language' => $request->get('language'),
        ]);

        if ($request->get('prefix')) {
            $article->prefix_id = (int) $request->get('prefix');
            $article->save();
        }

        if ($visibility == 'domestic_violence_page' && $request->get('chapter')) {
            $article->chapter()->create([
                'value' => $request->get('chapter'),
            ]);
        }

        if ($visibility == 'burnout_page' && $request->get('lesson')) {
            $article->lesson()->create([
                'value' => $request->get('lesson'),
            ]);
        }

        // thumbnail
        $file = $request->file('thumbnail-preview');
        $this->setThumbnail($file, $article, 'article');

        // visibilities
        $visibility = $article->createVisibilityFormat($request->get('categories'), $visibility);
        $article->setVisibility($visibility, $article->id, $request->get('start_date'), $request->get('end_date'), 'article');
        $this->setSections($article, $request->all('sections'), $request->get('lead'), $request->get('headline'), $request->get('language'));
        $article->setCategories($request->get('categories'));

        return redirect(route('admin.eap-online.articles.list'));
    }

    public function create()
    {
        return view('admin.eap-online.articles.new');
    }

    public function edit_view($id)
    {
        try {
            $article = EapArticle::query()->where('id', $id)->with(['eap_sections'])->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.articles.view', ['article' => $article]);
    }

    public function edit(Request $request, $id)
    {
        try {
            $article = EapArticle::query()->findOrFail($id);
            $type = $request->get('type');
            $visibility = $request->get('visibility');
            $categories = $request->get('categories');

            $validator = Validator::make($request->all(), [
                'headline' => ['required', 'string'],
                'lead' => ['required'],
                'thumbnail-preview' => ['max:5120'],
                'sections.*.content' => ['max:5120'],
            ]);

            if (! empty($visibility)) {
                $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

                $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
            }

            $validator->sometimes('prefix', 'required', fn (): bool => $type == 'rovat');

            $validator->sometimes('type', 'required', fn (): bool => $visibility == 'theme_of_the_month' || $visibility == 'home_page');

            if (! in_array($visibility, ['burnout_page', 'domestic_violence_page'])) {
                $validator->sometimes('categories', 'required', function () use ($categories): bool {
                    $all_articles_categories = EapCategory::query()->where('type', 'all-articles')->pluck('id')->toArray();

                    return count(array_intersect($all_articles_categories, $categories ?? [])) == 0;
                });
            }

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            if ($request->get('prefix')) {
                $article->prefix_id = $request->get('prefix');
            }

            if ($visibility != 'domestic_violence_page' && $article->chapter) {
                $article->chapter->delete();
            }

            if ($visibility != 'burnout_page' && $article->lesson) {
                $article->lesson->delete();
            }

            if ($visibility == 'domestic_violence_page' && $request->get('chapter')) {
                EapChapter::query()->updateOrCreate([
                    'chapterable_id' => $article->id,
                    'chapterable_type' => 'App\Models\Article',
                ], [
                    'value' => $request->get('chapter'),
                ]);
            }

            if ($visibility == 'burnout_page' && $request->get('lesson')) {
                EapLesson::query()->updateOrCreate([
                    'lessonable_id' => $article->id,
                    'lessonable_type' => 'App\Models\Article',
                ], [
                    'value' => $request->get('lesson'),
                ]);
            }

            if ($request->get('headline') != $article->getSectionByType('headline')) {
                $article->slug = $this->slugify($request->get('headline'), 'article');
            }

            $article->type = $request->get('type');

            if ($request->hasFile('thumbnail-preview')) {
                if ($old_thumbnail = $article->eap_thumbnail) {
                    Storage::delete('eap-online/thumbnails/article/'.$old_thumbnail->filename);
                    $article->eap_thumbnail()->delete();
                }

                $file = $request->file('thumbnail-preview');
                $this->setThumbnail($file, $article, 'article');
            }

            $visibility = $article->createVisibilityFormat($request->get('categories'), $visibility);

            $article->updateVisibility($visibility, $article->id, $request->get('start_date'), $request->get('end_date'), 'article');
            $this->setSections($article, $request->all('sections'), $request->get('lead'), $request->get('headline'), $article->input_language);
            $article->setCategories($request->get('categories'));

            $article->save();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.articles.list'));
    }

    public function delete($id)
    {
        try {
            $article = EapArticle::query()->findOrFail($id);

            if ($thumbnail = $article->eap_thumbnail) {
                Storage::delete('eap-online/thumbnails/article/'.$thumbnail->filename);
                $article->eap_thumbnail()->delete();
            }

            if ($article->lesson) {
                $article->lesson->delete();
            }

            if ($article->chapter) {
                $article->chapter->delete();
            }

            $article->eap_visibility()->delete();
            $article->eap_categories()->detach();
            $article->eap_sections()->each(function ($section): void {
                $section->eap_section_attachment()->each(function ($section_attachment): void {
                    Storage::delete('eap-online/section-attachments/'.$section_attachment->filename);
                    $section_attachment->delete();
                });

                $section->delete();
            });
            $article->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.articles.list'));
    }

    private function setSections($article, $sections, $lead, $headline, $language_id): void
    {
        $sections = array_shift($sections);
        // update article
        if ((is_countable($article->eap_sections) ? count($article->eap_sections) : 0) !== 0) {
            EapTranslation::query()->where([
                'language_id' => $article->input_language,
                'translatable_id' => $article->eap_sections->where('type', 'lead')->first()->id,
                'translatable_type' => 'App\Models\Section',
            ])->update([
                'value' => $lead,
            ]);

            EapTranslation::query()->where([
                'language_id' => $article->input_language,
                'translatable_id' => $article->eap_sections->where('type', 'headline')->first()->id,
                'translatable_type' => 'App\Models\Section',
            ])->update([
                'value' => $headline,
            ]);

            foreach ($sections as $section) {
                if (isset($section['id'])) {
                    if ($section['type'] == 'file' || $section['type'] == 'image') {
                        if (! empty($section['content'])) {
                            $curr_section = EapSection::query()->find($section['id']);
                            if ($old_attachment = $curr_section->eap_section_attachment()->where('language_id', $article->input_language)->first()) {
                                Storage::delete('eap-online/section-attachments/'.$old_attachment->filename);
                                $curr_section->eap_section_attachment()->where('language_id', $article->input_language)->first()->delete();
                            }
                            $this->setSectionAttachment($section['content'], $curr_section, $language_id);
                        }
                    } else {
                        $article->eap_sections->where('id', $section['id'])->first()->update([
                            'type' => $section['type'],
                        ]);

                        EapTranslation::query()->where([
                            'language_id' => $article->input_language,
                            'translatable_id' => $section['id'],
                            'translatable_type' => 'App\Models\Section',
                        ])->update([
                            'value' => $section['content'],
                        ]);
                    }
                } else {
                    $new_section = $article->eap_sections()->save(
                        new EapSection([
                            'type' => $section['type'],
                        ])
                    );

                    if ($section['type'] == 'file' || $section['type'] == 'image') {
                        $this->setSectionAttachment($section['content'], $new_section, $language_id);
                    } else {
                        $new_section->eap_section_translations()->save(new EapTranslation([
                            'value' => $section['content'],
                            'language_id' => $article->input_language,
                        ]));
                    }
                }
            }

            return;
        }

        // new article
        $headline_section = new EapSection([
            'type' => 'headline',
        ]);

        $lead_section = new EapSection([
            'type' => 'lead',
        ]);

        $article->eap_sections()->saveMany([
            $headline_section,
            $lead_section,
        ]);

        $headline_section->eap_section_translations()->save(new EapTranslation([
            'value' => $headline,
            'language_id' => $language_id,
        ]));

        $lead_section->eap_section_translations()->save(new EapTranslation([
            'value' => $lead,
            'language_id' => $language_id,
        ]));

        foreach ($sections as $section) {
            $new_section = new EapSection([
                'type' => $section['type'],
            ]);

            $article->eap_sections()->save($new_section);

            if ($section['type'] == 'file' || $section['type'] == 'image') {
                $this->setSectionAttachment($section['content'], $new_section, $language_id);
            } else {
                $new_section->eap_section_translations()->save(new EapTranslation([
                    'value' => $section['content'],
                    'language_id' => $language_id,
                ]));
            }
        }
    }

    public function delete_existing_article_section(Request $request)
    {
        $section_id = $request->get('id');

        try {
            $section = EapSection::query()->findOrFail($section_id);
            $section->delete();

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Section not found!', 404);
        }
    }

    public function delete_section_attachment_translation(Request $request)
    {
        try {
            $section = EapSection::query()->findOrFail($request->input('section_id'));
            $attachment = $section->eap_section_attachment()->where('language_id', $request->input('language_id'))->first();

            if (! empty($attachment)) {
                $attachment->delete();
            } else {
                return response('Section attachment not found!', 404);
            }

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Section not found!', 404);
        }
    }

    public function translate_view($id)
    {
        try {
            $article = EapArticle::query()->findOrFail($id);
            $languages = EapLanguage::all();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.articles.translate', ['article' => $article, 'languages' => $languages]);
    }

    public function translate(Request $request)
    {
        $request = $request->all('sections');
        $sections = array_shift($request);

        foreach ($sections as $section) {
            if (array_key_exists('text', $section)) {
                foreach ($section['text'] as $language_id => $translation) {
                    if ($section_translation = EapTranslation::query()->where(['translatable_type' => 'App\Models\Section', 'translatable_id' => $section['id'], 'language_id' => $language_id])->first()) {
                        $section_translation->value = $translation;
                        $section_translation->save();
                    } elseif (! empty($translation)) {
                        $parent_section = EapSection::query()->find($section['id']);
                        $parent_section->eap_section_translations()->save(new EapTranslation([
                            'value' => $translation,
                            'language_id' => $language_id,
                        ]));
                    }
                }
            } else {
                $section_model = EapSection::query()->find($section['id']);
                try {
                    $attachments = array_key_exists('image', $section) ? $section['image'] : $section['file'];

                    foreach ($attachments as $language_id => $attachment) {
                        if ($attachment == 'on') {
                            if ($old_attachment = $section_model->eap_section_attachment()->where('language_id', $language_id)->first()) {
                                $old_attachment->delete();
                            }

                            $section_model->eap_section_attachment()->save(new EapSectionAttachment([
                                'language_id' => $language_id,
                                'same' => true,
                            ]));
                        } else {
                            if ($old_attachment = $section_model->eap_section_attachment()->where('language_id', $language_id)->first()) {
                                Storage::delete('eap-online/section-attachments/'.$old_attachment->filename);
                                $section_model->eap_section_attachment()->where('language_id', $language_id)->first()->delete();
                            }

                            $this->setSectionAttachment($attachment, $section_model, $language_id);
                        }
                    }
                } catch (Exception) {
                }
            }
        }

        return redirect()->back();
    }

    private function setSectionAttachment($file, $section, $language_id): void
    {
        $name = time().'-'.$file->getClientOriginalName();
        $section_image = new EapSectionAttachment([
            'filename' => $name,
            'language_id' => $language_id,
        ]);

        $section->eap_section_attachment()->save($section_image);
        $file->storeAs('eap-online/section-attachments', $name);
    }
}
