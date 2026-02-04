<?php

namespace App\Http\Controllers\Admin\CompanyWebsite;

use App\Http\Controllers\Controller;
use App\Models\CompanyWebsite\Article;
use App\Models\CompanyWebsite\Language;
use App\Models\CompanyWebsite\Section;
use App\Models\CompanyWebsite\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ArticlesController extends Controller
{
    public function index()
    {
        return view('admin.compnay-website.articles.index');
    }

    public function get_articles(Request $request)
    {
        $articles = Article::query()
            ->with(['sections', 'sections.translations'])
            ->when(! empty($request->input('needle')), function ($query) use ($request): void {
                $needle = urldecode((string) $request->input('needle'));
                $query->whereHas('sections', function ($query2) use ($needle): void {
                    $query2->where('type', Section::TYPE_HEADLINE)->whereHas('translations', function ($query3) use ($needle): void {
                        $query3->where('value', 'like', '%'.$needle.'%');
                    });
                });
            })->paginate(5);

        return View::make('admin.compnay-website.articles.index-inner', ['articles' => $articles, 'translation' => $request->input('translation')])->render();
    }

    public function create()
    {
        return view('admin.compnay-website.articles.create');
    }

    public function store(Request $request)
    {
        validator()->validate($request->all(), [
            'headline' => ['required', 'string'],
            'lead' => ['required', 'string'],
            'language' => ['required'],
            'thumbnail-preview' => ['required', 'max:5120'],
        ]);

        DB::transaction(fn () => $this->create_article($request)
        );

        return redirect()->route('admin.company-website.articles.index');
    }

    public function edit(Article $article)
    {
        return view('admin.compnay-website.articles.edit', ['article' => $article]);
    }

    public function update(Article $article, Request $request)
    {
        validator()->validate($request->all(), [
            'headline' => ['required', 'string'],
            'lead' => ['required', 'string'],
            'thumbnail-preview' => ['max:5120'],
        ]);

        DB::transaction(fn () => $this->update_article($request, $article)
        );

        return redirect()->route('admin.company-website.articles.index');
    }

    public function delete(Article $article)
    {
        if (! empty($article->thumbnail)) {
            Storage::delete('company-website/thumbnails/'.$article->thumbnail);
        }

        $article->sections()->each(function ($section): void {
            $section->delete();
        });

        $article->delete();

        return redirect()->route('admin.company-website.articles.index');
    }

    public function translation_edit(Article $article)
    {
        $languages = Language::query()->get();

        return view('admin.compnay-website.articles.translation.edit', ['article' => $article, 'languages' => $languages]);
    }

    public function translation_update(Request $request)
    {
        foreach ($request->input('sections') as $section) {
            foreach ($section['text'] as $language_id => $translation) {
                if ($section_translation = Translation::query()->where(['translatable_type' => Section::class, 'translatable_id' => $section['id'], 'language_id' => $language_id])->first()) {
                    $section_translation->value = $translation;
                    $section_translation->save();
                } elseif (! empty($translation)) {
                    $parent_section = Section::query()->find($section['id']);
                    $parent_section->translations()->save(new Translation([
                        'value' => $translation,
                        'language_id' => $language_id,
                    ]));
                }
            }
        }

        return redirect()->back();
    }

    public function delete_existing_article_section(Request $request)
    {
        $section_id = $request->get('id');

        $section = Section::query()->findOrFail($section_id);
        $section->delete();

        return response('ok!');
    }

    private function update_article(Request $request, Article $article): void
    {
        $article->load('sections');

        if ($request->input('seo_title')) {
            $article->seo_title = $request->input('seo_title');
        }

        if ($request->input('seo_description')) {
            $article->seo_description = $request->input('seo_description');
        }

        if ($request->input('seo_keywords')) {
            $article->seo_keywords = $request->input('seo_keywords');
        }

        if ($request->hasFile('thumbnail-preview')) {
            if ($article->thumbnail) {
                Storage::delete('company-website/thumbnails/'.$article->thumbnail);
            }

            $file = $request->file('thumbnail-preview');
            $extension = $file->getClientOriginalExtension();
            $name = time().'-'.$article->slug.'.'.$extension;
            $article->thumbnail = $name;
            $article->save();
            $file->storeAs('company-website/thumbnails', $name);
        }

        if ($article->sections->count() !== 0) {
            Translation::query()->where([
                'language_id' => $article->input_language,
                'translatable_type' => Section::class,
                'translatable_id' => $article->sections->where('type', Section::TYPE_LEAD)->first()->id,
            ])->update(['value' => $request->input('lead')]);

            Translation::query()->where([
                'language_id' => $article->input_language,
                'translatable_type' => Section::class,
                'translatable_id' => $article->sections->where('type', Section::TYPE_HEADLINE)->first()->id,
            ])->update(['value' => $request->input('headline')]);

            foreach ($request->input('sections') as $section) {
                if (isset($section['id'])) {
                    $article->sections->where('id', $section['id'])->first()
                        ->update([
                            'type' => $section['type'],
                        ]);

                    Translation::query()->where([
                        'language_id' => $article->input_language,
                        'translatable_type' => Section::class,
                        'translatable_id' => $section['id'],
                    ])->update(['value' => $section['content']]);
                } else {
                    $new_section = new Section([
                        'type' => $section['type'],
                    ]);

                    $article->sections()->save($new_section);

                    $new_section->translations()->save(new Translation([
                        'value' => $section['content'],
                        'language_id' => $article->input_language,
                    ]));
                }
            }
        }

        $article->save();
    }

    private function create_article(Request $request): void
    {
        $article = Article::query()->create([
            'seo_title' => $request->input('seo_title'),
            'seo_description' => $request->input('seo_description'),
            'seo_keywords' => $request->input('seo_keywords'),
            'input_language' => $request->input('language'),
        ]);

        // thumbnail
        $file = $request->file('thumbnail-preview');
        $extension = $file->getClientOriginalExtension();
        $name = time().'-'.$article->slug.'.'.$extension;
        $article->thumbnail = $name;
        $article->save();
        $file->storeAs('company-website/thumbnails', $name);

        // sections
        $headline_section = new Section([
            'type' => Section::TYPE_HEADLINE,
        ]);

        $lead_section = new Section([
            'type' => Section::TYPE_LEAD,
        ]);

        $article->sections()->saveMany([$headline_section, $lead_section]);

        $headline_section->translations()->save(new Translation([
            'value' => $request->input('headline'),
            'language_id' => $request->input('language'),
        ]));

        $lead_section->translations()->save(new Translation([
            'value' => $request->input('lead'),
            'language_id' => $request->input('language'),
        ]));

        foreach ($request->input('sections') as $section) {
            $new_section = new Section([
                'type' => $section['type'],
            ]);

            $article->sections()->save($new_section);

            $new_section->translations()->save(new Translation([
                'value' => $section['content'],
                'language_id' => $request->input('language'),
            ]));
        }
    }
}
