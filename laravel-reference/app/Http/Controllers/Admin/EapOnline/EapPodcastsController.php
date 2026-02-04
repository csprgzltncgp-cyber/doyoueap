<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapPodcast;
use App\Models\EapOnline\EapPodcastAttachment;
use App\Traits\EapOnline\SlugifyTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use View;

class EapPodcastsController extends Controller
{
    use SlugifyTrait;

    public function index()
    {
        return view('admin.eap-online.podcasts.list');
    }

    public function get_podcasts(Request $request)
    {
        if (empty($request->get('needle'))) {
            $podcasts = EapPodcast::query()->paginate(5);
        } else {
            $needle = urldecode((string) $request->get('needle'));
            $podcasts = EapPodcast::query()->where('short_title', 'like', '%'.$needle.'%')->paginate(5);
        }

        return View::make('admin.eap-online.podcasts.list-inner', ['podcasts' => $podcasts])->render();
    }

    public function create()
    {
        return view('admin.eap-online.podcasts.new');
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

        $validator->sometimes('categories', 'required', function () use ($categories): bool {
            $all_podcast_categories = EapCategory::query()->where('type', 'all-podcasts')->pluck('id')->toArray();

            return count(array_intersect($all_podcast_categories, $categories ?? [])) == 0;
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $podcast = EapPodcast::query()->create(array_merge(
            $request->only(['link', 'long_title', 'short_title', 'description_first_line', 'description_second_line', 'language']),
            ['slug' => $this->slugify($request->get('short_title'), 'podcast')]
        ));

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $this->setAttachment($file, $podcast);
        }

        if ($request->has('download-button-text')) {
            $podcast->eap_podcast_attachment()->update([
                'button_text' => $request->get('download-button-text'),
            ]);
        }

        // visibilities
        $visibility = $podcast->createVisibilityFormat($request->get('categories'), $visibility);
        $podcast->setVisibility($visibility, $podcast->id, $request->get('start_date'), $request->get('end_date'), 'podcast');
        $podcast->setCategories($request->get('categories'));

        return redirect(route('admin.eap-online.podcasts.list'));
    }

    public function edit_view($id)
    {
        try {
            $podcast = EapPodcast::query()->where('id', $id)->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.podcasts.view', ['podcast' => $podcast]);
    }

    public function edit(Request $request, $id)
    {
        try {
            $podcast = EapPodcast::query()->findOrFail($id);
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

            $validator->sometimes('categories', 'required', function () use ($categories): bool {
                $all_podcasts_categories = EapCategory::query()->where('type', 'all-podcasts')->pluck('id')->toArray();

                return count(array_intersect($all_podcasts_categories, $categories ?? [])) == 0;
            });

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            if ($request->get('short_title') != $podcast->short_title) {
                $podcast->slug = $this->slugify($request->get('short_title'), 'podcast');
            }

            if ($request->hasFile('attachment')) {
                if ($old_attachment = $podcast->eap_podcast_attachment()->first()) {
                    Storage::delete('eap-online/podcast-attachments/'.$old_attachment->filename);
                    $podcast->eap_podcast_attachment->delete();
                }

                $file = $request->file('attachment');
                $this->setAttachment($file, $podcast);
            }

            if ($request->has('download-button-text')) {
                $podcast->eap_podcast_attachment()->update([
                    'button_text' => $request->get('download-button-text'),
                ]);
            }

            $visibility = $podcast->createVisibilityFormat($request->get('categories'), $visibility);

            $podcast->updateVisibility($visibility, $podcast->id, $request->get('start_date'), $request->get('end_date'), 'podcast');
            $podcast->setCategories($request->get('categories'));

            $podcast->update($request->only(['link', 'short_title', 'long_title', 'description_second_line', 'description_first_line']));
            $podcast->save();

        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.podcasts.list'));
    }

    public function delete($id)
    {
        try {
            $podcast = EapPodcast::query()->findOrFail($id);

            $podcast->eap_podcast_attachment()->each(function ($attachment): void {
                Storage::delete('eap-online/podcast-attachments/'.$attachment->filename);
                $attachment->delete();
            });

            $podcast->eap_visibility()->delete();
            $podcast->eap_categories()->detach();
            $podcast->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.podcasts.list'));
    }

    private function setAttachment($file, $podcast): void
    {
        $name = time().'-'.$file->getClientOriginalName();

        $attachment = new EapPodcastAttachment([
            'filename' => $name,
        ]);

        $podcast->eap_podcast_attachment()->save($attachment);
        $file->storeAs('eap-online/podcast-attachments', $name);
    }
}
