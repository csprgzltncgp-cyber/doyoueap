<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapChapter;
use App\Models\EapOnline\EapLesson;
use App\Models\EapOnline\EapWebinar;
use App\Traits\EapOnline\SlugifyTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EapWebinarsController extends Controller
{
    use SlugifyTrait;

    public function index()
    {
        return view('admin.eap-online.webinars.list');
    }

    public function get_webinars(Request $request)
    {
        $webinars = EapWebinar::query()
            ->when(! empty($request->input('needle')), function ($query) use ($request): void {
                $needle = urldecode((string) $request->input('needle'));
                $query->where('short_title', 'like', '%'.$needle.'%');
            })
            ->paginate(5);

        return View::make('admin.eap-online.webinars.list-inner', ['webinars' => $webinars, 'translation' => $request->get('translation')])->render();
    }

    public function create()
    {
        return view('admin.eap-online.webinars.new');
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
        ]);

        if (! empty($visibility)) {
            $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

            $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
        }

        if (! in_array($visibility, ['burnout_page', 'domestic_violence_page'])) {
            $validator->sometimes('categories', 'required', function () use ($categories): bool {
                $all_webinars_categories = EapCategory::query()->where('type', 'all-webinars')->pluck('id')->toArray();

                return count(array_intersect($all_webinars_categories, $categories ?? [])) == 0;
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $webinar = EapWebinar::query()->create(array_merge(
            $request->only(['link', 'long_title', 'short_title', 'description_first_line', 'description_second_line', 'language']),
            ['slug' => $this->slugify($request->get('short_title'), 'webinar')]
        ));

        if ($request->get('lesson')) {
            $webinar->lesson()->create([
                'value' => $request->get('lesson'),
            ]);
        }

        if ($request->get('chapter')) {
            $webinar->chapter()->create([
                'value' => $request->get('chapter'),
            ]);
        }

        // visibilities
        $visibility = $webinar->createVisibilityFormat($request->get('categories'), $visibility);
        $webinar->setVisibility($visibility, $webinar->id, $request->get('start_date'), $request->get('end_date'), 'webinar');
        $webinar->setCategories($request->get('categories'));

        return redirect(route('admin.eap-online.webinars.list'));
    }

    public function edit_view($id)
    {
        try {
            $webinar = EapWebinar::query()->where('id', $id)->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.webinars.view', ['webinar' => $webinar]);
    }

    public function edit(Request $request, $id)
    {
        try {
            $webinar = EapWebinar::query()->findOrFail($id);
            $visibility = $request->get('visibility');
            $categories = $request->get('categories');

            $validator = Validator::make($request->all(), [
                'short_title' => ['required', 'string'],
                'long_title' => ['required', 'string'],
                'link' => ['required', 'url'],
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

            if ($request->get('short_title') != $webinar->short_title) {
                $webinar->slug = $this->slugify($request->get('short_title'), 'webinar');
            }

            if ($visibility != 'burnout_page' && $webinar->lesson) {
                $webinar->lesson->delete();
            }

            if ($visibility != 'domestic_violence_page' && $webinar->chapter) {
                $webinar->chapter->delete();
            }

            if ($visibility == 'burnout_page' && $request->get('lesson')) {
                EapLesson::query()->updateOrCreate([
                    'lessonable_id' => $webinar->id,
                    'lessonable_type' => 'App\Models\Webinar',
                ], [
                    'value' => $request->get('lesson'),
                ]);
            }

            if ($visibility == 'domestic_violence_page' && $request->get('chapter')) {
                EapChapter::query()->updateOrCreate([
                    'chapterable_id' => $webinar->id,
                    'chapterable_type' => 'App\Models\Webinar',
                ], [
                    'value' => $request->get('chapter'),
                ]);
            }

            $visibility = $webinar->createVisibilityFormat($request->get('categories'), $visibility);

            $webinar->updateVisibility($visibility, $webinar->id, $request->get('start_date'), $request->get('end_date'), 'webinar');
            $webinar->setCategories($request->get('categories'));

            $webinar->update($request->only(['link', 'short_title', 'long_title', 'description_second_line', 'description_first_line']));
            $webinar->save();

        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.webinars.list'));
    }

    public function delete($id)
    {
        try {
            $webinar = EapWebinar::query()->findOrFail($id);

            if ($webinar->lesson) {
                $webinar->lesson->delete();
            }

            if ($webinar->chapter) {
                $webinar->chapter->delete();
            }

            $webinar->eap_visibility()->delete();
            $webinar->eap_categories()->detach();
            $webinar->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.webinars.list'));
    }
}
