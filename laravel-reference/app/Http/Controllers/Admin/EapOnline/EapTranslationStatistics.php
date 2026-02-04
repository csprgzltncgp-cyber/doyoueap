<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapArticle;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapQuiz;
use App\Models\EapOnline\EapVideo;
use Illuminate\Http\Request;

class EapTranslationStatistics extends Controller
{
    public function __invoke(Request $request)
    {
        $data = [];

        foreach (EapLanguage::all() as $language) {
            // articles
            $data['articles'][$language->name] = 0;
            foreach (EapArticle::all() as $article) {
                if (! $article->hasMissingTranslation($language->id)) {
                    $data['articles'][$language->name]++;
                }
            }

            // videos
            $data['videos'][$language->name] = 0;
            foreach (EapVideo::query()->where('all_languages', 1)->get() as $video) {
                if (! $video->hasMissingLanguageTranslation($language->id)) {
                    $data['videos'][$language->name]++;
                }
            }

            foreach (EapVideo::query()->where('all_languages', 0)->get() as $video) {
                if ($video->language == $language->id) {
                    $data['videos'][$language->name]++;
                }
            }

            // quizzes
            $data['quizzes'][$language->name] = 0;
            foreach (EapQuiz::all() as $quiz) {
                if (! $quiz->hasMissingTranslation($language->id)) {
                    $data['quizzes'][$language->name]++;
                }
            }
        }

        $data['article_count'] = EapArticle::query()->count();
        $data['video_count'] = EapVideo::query()->count();
        $data['quiz_count'] = EapQuiz::query()->count();

        return view('admin.eap-online.translation_statistics.index', ['data' => $data]);
    }
}
