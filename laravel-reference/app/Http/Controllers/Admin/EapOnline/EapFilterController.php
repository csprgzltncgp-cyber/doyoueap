<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapArticle;
use App\Models\EapOnline\EapCategory;
use App\Models\EapOnline\EapPodcast;
use App\Models\EapOnline\EapPrefix;
use App\Models\EapOnline\EapQuiz;
use App\Models\EapOnline\EapVideo;
use App\Models\EapOnline\EapWebinar;
use Illuminate\Http\Request;

class EapFilterController extends Controller
{
    public function filter_view($model)
    {
        switch ($model) {
            case 'articles':
                $groups = EapCategory::query()->where('type', '!=', 'all-videos')->where('type', '!=', 'all-podcasts')->where('parent_id', null)->with('childs')->get()->groupBy('type');
                $prefixes = EapPrefix::all();

                return view('admin.eap-online.filter.main', ['groups' => $groups, 'model' => $model, 'prefixes' => $prefixes]);
            case 'videos':
                $groups = EapCategory::query()->where('type', '!=', 'all-articles')->where('type', '!=', 'all-podcasts')->where('parent_id', null)->with('childs')->get()->groupBy('type');
                break;
            case 'podcasts':
                $groups = EapCategory::query()->where('type', '!=', 'all-articles')->where('type', '!=', 'all-videos')->where('parent_id', null)->with('childs')->get()->groupBy('type');
                break;
            case 'quizzes':
                $groups = EapCategory::query()->where('type', 'self-help')->where('parent_id', null)->with('childs')->get()->groupBy('type');
            default:
                $groups = EapCategory::query()->where('type', '!=', 'all-articles')->where('type', '!=', 'all-podcasts')->where('parent_id', null)->with('childs')->get()->groupBy('type');
        }

        return view('admin.eap-online.filter.main', ['groups' => $groups, 'model' => $model]);
    }

    public function filter(Request $request, $model)
    {
        $filters = array_filter($request->all());
        $query = EapArticle::query();

        switch ($model) {
            case 'articles':
                $query = EapArticle::query();
                break;
            case 'videos':
                $query = EapVideo::query();
                break;
            case 'webinars':
                $query = EapWebinar::query();
                break;
            case 'podcasts':
                $query = EapPodcast::query();
                break;
            case 'quizzes':
                $query = EapQuiz::query();
        }

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'date':
                    if (! empty($value[0]) && ! empty($value[1])) {
                        $query = $query->whereBetween('created_at', [$value[0], $value[1]]);
                    }
                    break;
                case 'visibility':
                    $query = $query->whereHas('eap_visibility', function ($q) use ($value): void {
                        $q->where($value, 1);
                    });
                    break;
                case 'category':
                    $query = $query->whereHas('eap_categories', function ($q) use ($value): void {
                        $q->where('categories.id', $value);
                    });
                    break;
                case 'prefix':
                    $query = $query->where('prefix_id', $value);
                    break;
                case 'type':
                    $query = $query->where('type', $value);
                    break;
            }
        }

        $models = $query->get();

        return view('admin.eap-online.filter.result', ['models' => $models, 'model' => $model]);
    }
}
