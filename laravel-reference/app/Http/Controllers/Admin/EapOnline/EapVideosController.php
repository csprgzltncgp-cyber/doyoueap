<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapChapter;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapLesson;
use App\Models\EapOnline\EapTranslation;
use App\Models\EapOnline\EapVideo;
use App\Models\EapOnline\EapVideoAttachment;
use App\Traits\EapOnline\SlugifyTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EapVideosController extends Controller
{
    use SlugifyTrait;

    public function index()
    {
        return view('admin.eap-online.videos.list');
    }

    public function get_videos(Request $request)
    {
        $videos = EapVideo::query()
            ->when(! empty($request->input('needle')), function ($query) use ($request): void {
                $needle = urldecode((string) $request->input('needle'));
                $query->where('short_title', 'like', '%'.$needle.'%');
            })
            ->when($request->input('translation'), function ($query): void {
                $query->where('all_languages', true);
            })
            ->paginate(5);

        return View::make('admin.eap-online.videos.list-inner', ['videos' => $videos, 'translation' => $request->get('translation')])->render();
    }

    public function create()
    {
        return view('admin.eap-online.videos.new');
    }

    public function store(Request $request)
    {
        $visibility = $request->get('visibility');
        $categories = $request->get('categories');

        $validator = Validator::make($request->all(), [
            'language' => ['required'],
            'short_title' => ['required', 'string'],
            'long_title' => ['required', 'string'],
            'link' => ['required', 'url'],
            'download-button-text' => ['required_with:attachment'],
            'attachment' => ['required_with:download-button-text'],
        ]);

        if (! empty($visibility)) {
            $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

            $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
        }

        if (! in_array($visibility, ['burnout_page', 'domestic_violence_page'])) {
            $validator->sometimes('categories', 'required', function () use ($categories): bool {
                $all_videos_categories = EapCategory::query()->where('type', 'all-videos')->pluck('id')->toArray();

                return count(array_intersect($all_videos_categories, $categories ?? [])) == 0;
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $video = EapVideo::query()->create(array_merge(
            $request->only(['link', 'long_title', 'short_title', 'description_first_line', 'description_second_line', 'all_languages', 'language']),
            ['slug' => $this->slugify($request->get('short_title'), 'video')]
        ));

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $this->setAttachment($file, $video, $request->input('language'));
        }

        if ($request->has('download-button-text')) {
            $video->eap_video_attachment()->where('language_id', $request->input('language'))->update([
                'button_text' => $request->get('download-button-text'),
            ]);
        }

        if ($request->get('lesson')) {
            $video->lesson()->create([
                'value' => $request->get('lesson'),
            ]);
        }

        if ($request->get('chapter')) {
            $video->chapter()->create([
                'value' => $request->get('chapter'),
            ]);
        }

        // translations
        if ($request->has('all_languages')) {
            $this->save_translations_on_mutation($request, $video);
        }

        // visibilities
        $visibility = $video->createVisibilityFormat($request->get('categories'), $visibility);
        $video->setVisibility($visibility, $video->id, $request->get('start_date'), $request->get('end_date'), 'video');
        $video->setCategories($request->get('categories'));

        return redirect(route('admin.eap-online.videos.list'));
    }

    public function edit_view($id)
    {
        try {
            $video = EapVideo::query()->where('id', $id)->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.videos.view', ['video' => $video]);
    }

    public function edit(Request $request, $id)
    {
        try {
            $video = EapVideo::query()->findOrFail($id);
            $visibility = $request->get('visibility');
            $categories = $request->get('categories');

            $validator = Validator::make($request->all(), [
                'short_title' => ['required', 'string'],
                'long_title' => ['required', 'string'],
                'link' => ['required', 'url'],
                'download-button-text' => ['required_with:attachment'],
                'visibility' => ['required'],
            ]);

            if (! empty($visibility)) {
                $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

                $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
            }

            if (! in_array($visibility, ['burnout_page', 'domestic_violence_page'])) {
                $validator->sometimes('categories', 'required', function () use ($categories): bool {
                    $all_articles_categories = EapCategory::query()->where('type', 'all-articles')->pluck('id')->toArray();

                    return count(array_intersect($all_articles_categories, $categories ?? [])) == 0;
                });
            }

            if ($validator->fails()) {

                return back()->withErrors($validator);
            }

            if ($request->get('short_title') != $video->short_title) {
                $video->slug = $this->slugify($request->get('short_title'), 'video');
            }

            if ($request->hasFile('attachment')) {
                if ($old_attachment = $video->eap_video_attachment()->where('language_id', $video->language)->first()) {
                    Storage::delete('eap-online/video-attachments/'.$old_attachment->filename);
                    $video->eap_video_attachment()->where('language_id', $video->language)->first()->delete();
                }

                $file = $request->file('attachment');
                $this->setAttachment($file, $video, $video->language);
            }

            if ($request->has('download-button-text')) {
                $video->eap_video_attachment()->where('language_id', $video->language)->update([
                    'button_text' => $request->get('download-button-text'),
                ]);
            }

            if ($visibility != 'burnout_page' && $video->lesson) {
                $video->lesson->delete();
            }

            if ($visibility != 'domestic_violence_page' && $video->chapter) {
                $video->chapter->delete();
            }

            if ($visibility == 'burnout_page' && $request->get('lesson')) {
                EapLesson::query()->updateOrCreate([
                    'lessonable_id' => $video->id,
                    'lessonable_type' => 'App\Models\Video',
                ], [
                    'value' => $request->get('lesson'),
                ]);
            }

            if ($visibility == 'domestic_violence_page' && $request->get('chapter')) {
                EapChapter::query()->updateOrCreate([
                    'chapterable_id' => $video->id,
                    'chapterable_type' => 'App\Models\Video',
                ], [
                    'value' => $request->get('chapter'),
                ]);
            }

            $visibility = $video->createVisibilityFormat($request->get('categories'), $visibility);

            $video->updateVisibility($visibility, $video->id, $request->get('start_date'), $request->get('end_date'), 'video');
            $video->setCategories($request->get('categories'));

            $video->update($request->only(['link', 'short_title', 'long_title', 'description_second_line', 'description_first_line']));
            $video->save();

            // translations
            if ($video->all_languages) {
                $this->save_translations_on_mutation($request, $video);
            }

        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.videos.list'));
    }

    public function delete($id)
    {
        try {
            $video = EapVideo::query()->findOrFail($id);

            $video->eap_video_attachment()->each(function ($attachment): void {
                Storage::delete('eap-online/video-attachments/'.$attachment->filename);
                $attachment->delete();
            });

            if ($video->lesson) {
                $video->lesson->delete();
            }

            if ($video->chapter) {
                $video->chapter->delete();
            }

            $video->eap_visibility()->delete();
            $video->eap_categories()->detach();
            $video->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.videos.list'));
    }

    public function translate_list()
    {
        return view('admin.eap-online.videos.list');
    }

    public function translate_view($id)
    {
        try {
            $video = EapVideo::query()->find($id);
            $languages = EapLanguage::all();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.videos.translate', ['video' => $video, 'languages' => $languages]);
    }

    public function translate(Request $request)
    {
        try {
            $video = EapVideo::query()->findOrFail($request->get('video_id'));

            $this->save_translations('ShortTitle', $request->get('short_title'), $video->id);
            $this->save_translations('LongTitle', $request->get('long_title'), $video->id);
            $this->save_translations('DescriptionFirstLine', $request->get('description_first_line'), $video->id);
            $this->save_translations('DescriptionSecondLine', $request->get('description_second_line'), $video->id);

            // attachments and download button text
            $attachments = $request->all('attachments');
            $attachments = array_shift($attachments);
            if (! empty($attachments)) {
                foreach ($attachments as $language_id => $attachment) {
                    if (empty($attachment)) {
                        continue;
                    }

                    if ($old_attachment = $video->eap_video_attachment()->where('language_id', $language_id)->first()) {
                        Storage::delete('eap-online/video-attachments/'.$old_attachment->filename);
                        $video->eap_video_attachment()->where('language_id', $language_id)->first()->delete();
                    }

                    $this->setAttachment($attachment, $video, $language_id);
                }
            }

            if ($request->has('button_text')) {
                foreach ($request->input('button_text') as $language_id => $translation) {
                    if ($attachment = $video->eap_video_attachment()->where('language_id', $language_id)->first()) {
                        $attachment->update([
                            'button_text' => $translation,
                        ]);
                    }
                }
            }
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect()->back();
    }

    private function save_translations_on_mutation(Request $request, $video): void
    {
        $this->save_translation($request->input('short_title'), 'ShortTitle', $video->language, $video->id);
        $this->save_translation($request->input('long_title'), 'LongTitle', $video->language, $video->id);

        if ($request->has('description_first_line')) {
            $this->save_translation($request->input('description_first_line'), 'DescriptionFirstLine', $video->language, $video->id);
        }

        if ($request->has('description_second_line')) {
            $this->save_translation($request->input('description_second_line'), 'DescriptionSecondLine', $video->language, $video->id);
        }
    }

    private function save_translation($translation, string $type, $language_id, $video_id): void
    {
        if ($video_translation = EapTranslation::query()->where(['translatable_type' => "App\Models\Video\\{$type}", 'translatable_id' => $video_id, 'language_id' => $language_id])->first()) {
            $video_translation->value = $translation;
            $video_translation->language_id = $language_id;
            $video_translation->save();
        } elseif (! empty($translation)) {
            EapTranslation::query()->create([
                'translatable_id' => $video_id,
                'translatable_type' => 'App\Models\Video\\'.$type,
                'language_id' => $language_id,
                'value' => $translation,
            ]);
        }
    }

    private function save_translations(string $type, $translations, $video_id): void
    {
        foreach ($translations as $language_id => $translation) {
            $this->save_translation($translation, $type, $language_id, $video_id);
        }
    }

    private function setAttachment($file, $video, $language_id): void
    {
        $name = time().'-'.$file->getClientOriginalName();

        $attachment = new EapVideoAttachment([
            'filename' => $name,
            'language_id' => $language_id,
        ]);

        $video->eap_video_attachment()->save($attachment);
        $file->storeAs('eap-online/video-attachments', $name);
    }
}
