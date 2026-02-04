<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapAnswer;
use App\Models\EapOnline\EapChapter;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapLesson;
use App\Models\EapOnline\EapQuestion;
use App\Models\EapOnline\EapQuiz;
use App\Models\EapOnline\EapResult;
use App\Models\EapOnline\EapTranslation;
use App\Traits\EapOnline\SlugifyTrait;
use App\Traits\EapOnline\ThumbnailTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EapQuizzesController extends Controller
{
    use SlugifyTrait;
    use ThumbnailTrait;

    public function index()
    {
        return view('admin.eap-online.quizzes.list');
    }

    public function get_quizzes(Request $request)
    {
        $quizzes = EapQuiz::query()
            ->when(! empty($request->input('needle')), function ($query) use ($request): void {
                $needle = urldecode((string) $request->input('needle'));
                $query->whereHas('title_translations', function ($query2) use ($needle): void {
                    $query2->where(function ($query3): void {
                        $query3->where('language_id', 3)->orWhere('language_id', 1);
                    })->where('value', 'like', '%'.$needle.'%');
                });
            })->paginate(5);

        return View::make('admin.eap-online.quizzes.list-inner', ['quizzes' => $quizzes, 'translation' => $request->get('translation')])->render();
    }

    public function create()
    {
        return view('admin.eap-online.quizzes.new');
    }

    public function store(Request $request)
    {
        $visibility = $request->get('visibility');

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'questions' => ['required'],
            'results' => ['required'],
            'language' => ['required'],
            'thumbnail' => ['max:5120'],
        ]);

        if (! empty($visibility)) {
            $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

            $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quiz = EapQuiz::query()->create([
            'slug' => $this->slugify($request->get('title'), 'quiz'),
            'input_language' => $request->get('language'),
        ]);

        EapTranslation::query()->create([
            'value' => $request->get('title'),
            'language_id' => $quiz->input_language,
            'translatable_type' => 'App\Models\Quiz',
            'translatable_id' => $quiz->id,
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $this->setThumbnail($file, $quiz, 'quiz');
        }

        if ($request->get('lesson')) {
            $quiz->lesson()->create([
                'value' => $request->get('lesson'),
            ]);
        }

        if ($request->get('chapter')) {
            $quiz->chapter()->create([
                'value' => $request->get('chapter'),
            ]);
        }

        // visibilities
        $visibility = $quiz->createVisibilityFormat($request->get('categories'), $visibility);

        $quiz->setVisibility($visibility, $quiz->id, $request->get('start_date'), $request->get('end_date'), 'quiz');
        $quiz->setCategories($request->get('categories'));
        $this->setQuestions($quiz, $request->get('questions'));
        $this->setResults($quiz, $request->get('results'));

        return redirect(route('admin.eap-online.quizzes.list'));
    }

    public function edit_view($id)
    {
        try {
            $quiz = EapQuiz::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.quizzes.view', ['quiz' => $quiz]);
    }

    public function edit(Request $request, $id)
    {
        try {
            $quiz = EapQuiz::query()->findOrFail($id);
            $visibility = $request->get('visibility');

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string'],
                'questions' => ['required'],
                'results' => ['required'],
                'visibility' => ['required'],
                'thumbnail' => ['max:5120'],
            ]);

            if (! empty($visibility)) {
                $validator->sometimes('start_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');

                $validator->sometimes('end_date', 'required', fn (): bool => $visibility == 'theme_of_the_month');
            }

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            if ($request->get('title') != $quiz->title_translations()->where('language_id', $quiz->input_language)->first()->value) {
                $quiz->slug = $this->slugify($request->get('title'), 'quiz');
            }

            EapTranslation::query()->where(['language_id' => $quiz->input_language, 'translatable_id' => $quiz->id, 'translatable_type' => 'App\Models\Quiz'])->update([
                'value' => $request->get('title'),
            ]);

            if ($request->hasFile('thumbnail')) {
                if ($old_thumbnail = $quiz->eap_thumbnail) {
                    Storage::delete('eap-online/thumbnails/quiz/'.$old_thumbnail->filename);
                    $quiz->eap_thumbnail()->delete();
                }

                $file = $request->file('thumbnail');
                $this->setThumbnail($file, $quiz, 'quiz');
            }

            if ($visibility != 'burnout_page' && $quiz->lesson) {
                $quiz->lesson->delete();
            }

            if ($visibility != 'domestic_violence_page' && $quiz->chapter) {
                $quiz->chapter->delete();
            }

            if ($visibility == 'burnout_page' && $request->get('lesson')) {
                EapLesson::query()->updateOrCreate([
                    'lessonable_id' => $quiz->id,
                    'lessonable_type' => 'App\Models\Quiz',
                ], [
                    'value' => $request->get('lesson'),
                ]);
            }

            if ($visibility == 'domestic_violence_page' && $request->get('chapter')) {
                EapChapter::query()->updateOrCreate([
                    'chapterable_id' => $quiz->id,
                    'chapterable_type' => 'App\Models\Quiz',
                ], [
                    'value' => $request->get('chapter'),
                ]);
            }

            $visibility = $quiz->createVisibilityFormat($request->get('categories'), $visibility);

            $quiz->updateVisibility($visibility, $quiz->id, $request->get('start_date'), $request->get('end_date'), 'quiz');
            $quiz->setCategories($request->get('categories'));
            $this->setQuestions($quiz, $request->get('questions'));
            $this->setResults($quiz, $request->get('results'));

            $quiz->save();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.quizzes.list'));
    }

    public function delete($id)
    {
        try {
            $quiz = EapQuiz::query()->findOrFail($id);

            if ($thumbnail = $quiz->eap_thumbnail) {
                Storage::delete('eap-online/thumbnails/quiz/'.$thumbnail->filename);
                $quiz->eap_thumbnail()->delete();
            }

            if ($quiz->lesson) {
                $quiz->lesson->delete();
            }

            if ($quiz->chapter) {
                $quiz->chapter->delete();
            }

            $quiz->delete();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return redirect(route('admin.eap-online.quizzes.list'));
    }

    private function setResults($quiz, $results): void
    {
        // update result
        if ((is_countable($quiz->eap_results) ? count($quiz->eap_results) : 0) > 0) {
            foreach ($results as $result_id => $result) {
                EapTranslation::query()->where(['language_id' => $quiz->input_language, 'translatable_type' => 'App\Models\Result', 'translatable_id' => $result_id])
                    ->update([
                        'value' => $result['content'],
                    ]);

                EapResult::query()->where(['id' => $result_id])->update([
                    'from' => (int) $result['from'],
                    'to' => (int) $result['to'],
                ]);
            }

            return;
        }

        // new result
        foreach ($results as $result) {
            $new_result = new EapResult([
                'from' => (int) $result['from'],
                'to' => (int) $result['to'],
            ]);
            $quiz->eap_results()->save($new_result);
            $new_result->translations()->save(new EapTranslation([
                'value' => $result['content'],
                'language_id' => $quiz->input_language,
            ]));
        }
    }

    private function setQuestions($quiz, $questions): void
    {
        // update questions and answers
        if ((is_countable($quiz->eap_questions) ? count($quiz->eap_questions) : 0) > 0) {
            foreach ($questions as $question_id => $question) {
                EapTranslation::query()->where(['language_id' => $quiz->input_language, 'translatable_type' => 'App\Models\Question', 'translatable_id' => $question_id])
                    ->update([
                        'value' => $question['title'],
                    ]);

                foreach ($question['answers'] as $answer_id => $answer) {
                    EapTranslation::query()->where(['language_id' => $quiz->input_language, 'translatable_type' => 'App\Models\Answer', 'translatable_id' => $answer_id])
                        ->update([
                            'value' => $answer['title'],
                        ]);

                    EapAnswer::query()->where('id', $answer_id)->update([
                        'point' => $answer['point'],
                    ]);
                }
            }

            return;
        }

        // new questions and answers
        foreach ($questions as $question) {
            $new_question = new EapQuestion;
            $quiz->eap_questions()->save($new_question);
            $new_question->translations()->save(new EapTranslation([
                'value' => $question['title'],
                'language_id' => $quiz->input_language,
            ]));

            foreach ($question['answers'] as $answer) {
                $new_answer = new EapAnswer([
                    'point' => $answer['point'],
                ]);
                $new_question->eap_answers()->save($new_answer);
                $new_answer->translations()->save(new EapTranslation([
                    'value' => $answer['title'],
                    'language_id' => $quiz->input_language,
                ]));
            }
        }
    }

    public function translate_view($id)
    {
        try {
            $quiz = EapQuiz::query()->findOrFail($id);
            $languages = EapLanguage::all();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('admin.eap-online.quizzes.translate', ['quiz' => $quiz, 'languages' => $languages]);
    }

    public function translate(Request $request)
    {
        $this->updateOrCreateTranslation($request->get('title'), 'App\Models\Quiz');
        $this->updateOrCreateTranslation($request->get('questions'), 'App\Models\Question');
        $this->updateOrCreateTranslation($request->get('answers'), 'App\Models\Answer');
        $this->updateOrCreateTranslation($request->get('results'), 'App\Models\Result');

        return redirect()->back();
    }

    private function updateOrCreateTranslation($resource_array, string $resource_type): void
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
}
