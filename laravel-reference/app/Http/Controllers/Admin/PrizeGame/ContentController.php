<?php

namespace App\Http\Controllers\Admin\PrizeGame;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;
use App\Models\EapOnline\EapLanguage;
use App\Models\PrizeGame\Answer;
use App\Models\PrizeGame\Content;
use App\Models\PrizeGame\Digit;
use App\Models\PrizeGame\Image;
use App\Models\PrizeGame\Question;
use App\Models\PrizeGame\Section;
use App\Models\PrizeGame\Type;
use App\Traits\DocumentTrait;
use App\Traits\Prizegame\ContentTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    use ContentTrait;
    use DocumentTrait;

    public function index()
    {
        return view('admin.prizegame.contents.menu');
    }

    public function list_content($list)
    {
        $contents = Content::query()->with(['language'])->orderBy('company_id')->get();
        $companies = Company::query()->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 2))->orderBy('name')->get();
        $types = Type::query()->get();
        $countries = Country::query()->get();
        $languages = EapLanguage::all();

        if ($list == 'template') {
            $contents = $contents->filter(fn ($item): bool => empty($item->country) || empty($item->company));
        } else {
            $contents = $contents->filter(fn ($item): bool => ! empty($item->country) || ! empty($item->company));
        }

        return view('admin.prizegame.contents.index', ['contents' => $contents, 'companies' => $companies, 'countries' => $countries, 'types' => $types, 'languages' => $languages, 'list' => $list]);
    }

    public function create()
    {
        $types = Type::query()->get();
        $languages = EapLanguage::all();

        if (! Session::has('prizegame_type')) {
            Session::put('prizegame_type', $types->first()->id);
        }

        return view('admin.prizegame.contents.create', ['types' => $types, 'languages' => $languages]);
    }

    public function store()
    {
        if (Content::query()->where('type_id', request()->input('type'))->where('language_id', request()->input('language'))->exists()) {
            session()->flash('duplicate_content_warning');

            return redirect()->route('admin.prizegame.pages.list', ['list' => 'template']);
        }

        $content = Content::query()->create([
            'language_id' => request()->input('language'),
            'type_id' => request()->input('type'),
        ]);

        if (request()->has('sections')) {
            foreach (request()->all('sections')['sections'] as $section) {
                $new_section = new Section([
                    'type' => $section['type'],
                    'block' => $section['block'],
                ]);

                $new_section = $content->sections()->save($new_section);

                $new_section->translations()->create([
                    'language_id' => request()->input('language'),
                    'value' => $section['value'] ?: '',
                ]);

                if (! array_key_exists('document', $section)) {
                    continue;
                }
                if ($section['type'] != Section::TYPE_CHECKBOX) {
                    continue;
                }

                $this->save_document($section['document']['file'], $section['document']['name'], $new_section, 'eap-online/prizegame/');
            }
        }

        if (request()->hasFile('bg-image')) {
            $this->save_image(request()->file('bg-image'), $content);
        }

        if (request()->has('questions')) {
            foreach (request()->input('questions') as $question_id => $question_data) {
                $question = new Question;

                $question = $content->questions()->save($question);

                $question->translations()->create([
                    'language_id' => request()->input('language'),
                    'value' => $question_data['title'],
                ]);

                foreach (request()->input('digits') as $digit_id => $digit_data) {
                    if (! empty($digit_data['question_id']) && (int) $digit_data['question_id'] === (int) $question_id) {
                        $question->digit()->save(new Digit([
                            'value' => $digit_data['value'],
                            'content_id' => $content->id,
                            'order' => $digit_data['sort'],
                        ]));

                        unset(request()->input('digits')[$digit_id]);
                    }
                }

                if (array_key_exists('answers', $question_data)) {
                    foreach ($question_data['answers'] as $answer_id => $answer_data) {
                        $answer = new Answer([
                            'correct' => $answer_id == $question_data['correct'],
                        ]);

                        $answer = $question->answers()->save($answer);

                        $answer->translations()->create([
                            'language_id' => request()->input('language'),
                            'value' => $answer_data['title'],
                        ]);
                    }
                }
            }
        }

        if (request()->has('digits')) {
            foreach (request()->input('digits') as $digit_data) {
                if (empty($digit_data['question_id'])) {
                    $content->digits()->save(new Digit([
                        'value' => $digit_data['value'],
                        'order' => $digit_data['sort'],
                    ]));
                }
            }
        }

        if (Session::has('prizegame_type')) {
            Session::forget('prizegame_type');
        }

        return redirect()->route('admin.prizegame.pages.index');
    }

    public function save_as()
    {
        request()->validate([
            'content_id' => ['required'],
            'type_id' => ['required'],
            'language_id' => ['required'],
        ]);

        $new_content_id = $this->recreate_content(request()->input('content_id'), null, null, request()->input('language_id'));

        Content::query()->where('id', $new_content_id)->update([
            'type_id' => request()->input('type_id'),
            'language_id' => request()->input('language_id'),
        ]);

        return response()->json('OK!');
    }

    public function edit(Content $content)
    {
        Session::put('game_redirect_url', url()->previous());
        $content->load(['sections', 'questions' => fn ($query) => $query->has('digit'), 'digits', 'image', 'questions.answers', 'sections.documents', 'questions.translations', 'questions.answers.translations', 'sections.translations', 'sections.documents.translations']);

        return view('admin.prizegame.contents.edit', ['content' => $content]);
    }

    public function update(Content $content)
    {
        if (request()->has('sections')) {
            foreach (request()->all('sections')['sections'] as $section) {
                if (array_key_exists('id', $section)) {
                    $section_model = $content->sections()->where('id', $section['id'])->first();
                    $section_model->update([
                        'type' => $section['type'],
                    ]);

                } else {
                    $section_model = new Section([
                        'type' => $section['type'],
                        'block' => $section['block'],
                    ]);

                    $content->sections()->save($section_model);
                }

                $section_model->translations()->updateOrCreate([
                    'language_id' => $content->language_id,
                ], [
                    'value' => $section['value'],
                ]);

                if (! array_key_exists('document', $section)) {
                    continue;
                }
                if ($section['type'] != Section::TYPE_CHECKBOX) {
                    continue;
                }
                if (array_key_exists('file', $section['document'])) {
                    $this->update_document($section['document']['file'], $section['document']['name'], $section_model, 'eap-online/prizegame/');
                } elseif ($section_model->documents) {
                    $section_model->documents->translations()->updateOrCreate([
                        'language_id' => $content->language_id,
                    ], [
                        'value' => $section['document']['name'],
                    ]);
                }
            }
        }

        if (request()->hasFile('bg-image')) {
            $this->update_image(request()->file('bg-image'), $content);
        }

        if (request()->input('phone-number-changed')) {
            $content->answers()->delete();
            $content->digits()->delete();
            $content->questions()->delete();
        }

        if (request()->has('questions')) {
            foreach (request()->input('questions') as $question_id => $question_data) {
                if (array_key_exists('id', $question_data)) {
                    $question = $content->questions()->where('id', $question_data['id'])->first();

                    $question->translations()->updateOrCreate([
                        'language_id' => $content->language_id,
                    ], [
                        'value' => $question_data['title'],
                    ]);

                    $this->set_digits(request()->input('digits'), $question, $question_id, $content);

                    if (array_key_exists('answers', $question_data)) {
                        foreach ($question_data['answers'] as $answer_id => $answer_data) {
                            if (array_key_exists('id', $answer_data)) {
                                $question->answers()->where('id', $answer_data['id'])->update([
                                    'correct' => $answer_data['id'] == $question_data['correct'],
                                ]);

                                $question->answers()->where('id', $answer_data['id'])->first()->translations()->updateOrCreate([
                                    'language_id' => $content->language_id,
                                ], [
                                    'value' => $answer_data['title'],
                                ]);
                            } else {
                                $answer = new Answer([
                                    'correct' => $answer_id == $question_data['correct'],
                                ]);

                                $answer = $question->answers()->save($answer);

                                $answer->translations()->create([
                                    'language_id' => $content->language_id,
                                    'value' => $answer_data['title'],
                                ]);
                            }

                        }
                    }

                    $question->save();
                } else {
                    $question = new Question;

                    $question = $content->questions()->save($question);

                    $question->translations()->create([
                        'language_id' => $content->language_id,
                        'value' => $question_data['title'],
                    ]);

                    $this->set_digits(request()->input('digits'), $question, $question_id, $content);

                    if (array_key_exists('answers', $question_data)) {
                        foreach ($question_data['answers'] as $answer_id => $answer_data) {
                            $answer = new Answer([
                                'correct' => $answer_id == $question_data['correct'],
                            ]);

                            $answer = $question->answers()->save($answer);

                            $answer->translations()->create([
                                'language_id' => $content->language_id,
                                'value' => $answer_data['title'],
                            ]);
                        }
                    }
                }
            }
        }

        if (request()->has('digits')) {
            foreach (request()->input('digits') as $digit_data) {
                if (array_key_exists('id', $digit_data)) {
                    if (empty($digit_data['question_id'])) {
                        $content->digits()->where('id', $digit_data['id'])->update([
                            'value' => $digit_data['value'],
                        ]);
                    }
                } elseif (empty($digit_data['question_id'])) {
                    $content->digits()->save(new Digit([
                        'value' => $digit_data['value'],
                        'order' => $digit_data['sort'],
                    ]));
                }
            }
        }

        return redirect(Session::get('game_redirect_url'));
    }

    public function delete(Content $content)
    {
        $content->delete();

        return response('ok!');
    }

    public function delete_existing_section()
    {
        $id = request()->input('id');

        try {
            $section = Section::query()->findOrFail($id);
            $section->delete();

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Sections not found!', 404);
        }
    }

    public function delete_existing_document()
    {
        $id = request()->input('id');

        try {
            $section = Section::query()->findOrFail($id);

            if ($section->documents()->exists()) {
                Storage::delete('eap-online/prizegame/documents/'.$section->documents->filename);
                $section->documents->delete();
            }

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Section not found!', 404);
        }
    }

    public function delete_existing_image()
    {
        $id = request()->input('id');

        try {
            $content = Content::query()->findOrFail($id);

            if ($content->image()->exists()) {
                Storage::delete('eap-online/prizegame/images/'.$content->image->filename);
                $content->image->delete();
            }

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Content not found!', 404);
        }
    }

    public function delete_existing_question()
    {
        $id = request()->input('id');

        try {
            $question = Question::query()->findOrFail($id);
            $content = $question->content;

            foreach ($question->answers as $answer) {
                $answer->delete();
            }

            if ($question->digit) {
                $question->digit->update([
                    'question_id' => null,
                ]);
            }

            $content->questions()->where('id', $question->id)->delete();

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Answer not found!', 404);
        }
    }

    public function delete_existing_answer()
    {
        $id = request()->input('id');

        try {
            $answer = Answer::query()->findOrFail($id);
            $answer->delete();

            return response('ok!');
        } catch (ModelNotFoundException) {
            return response('Answer not found!', 404);
        }
    }

    public function has_content_like()
    {
        request()->validate([
            'language_id' => ['required'],
            'type_id' => ['required'],
            'company_id' => ['required'],
            'country_id' => ['required'],
        ]);

        $exists = Content::query()->where([
            'language_id' => request()->input('language_id'),
            'type_id' => request()->input('type_id'),
            'company_id' => request()->input('company_id'),
            'country_id' => request()->input('country_id'),
        ])->exists();

        return response()->json($exists);
    }

    private function update_image($file, Content $content): void
    {
        if ($old_image = $content->image()->first()) {
            Storage::delete('eap-online/prizegame/images/'.$old_image->filename);
            $old_image->delete();
        }

        $this->save_image($file, $content);
    }

    private function save_image($file, $content): void
    {
        $extension = $file->getClientOriginalExtension();
        $filename = time().'-'.Str::random(10).'.'.$extension;

        $content->image()->save(new Image([
            'filename' => $filename,
        ]));

        $file->storeAs('eap-online/prizegame/images/', $filename);
    }

    private function set_digits(array $digits, $question, $question_id, Content $content): void
    {
        foreach ($digits as $digit_id => $digit_data) {
            if ((int) $digit_data['question_id'] === (int) $question_id) {
                Digit::query()->updateOrCreate([
                    'id' => $digit_data['id'] ?? null,
                    'order' => $digit_data['sort'],
                ], [
                    'value' => $digit_data['value'],
                    'content_id' => $content->id,
                    'question_id' => $question->id,
                ]);

                unset($digits[$digit_id]);
            }
        }
    }

    public function set_prizegame_type(): void
    {
        Session::put('prizegame_type', request()->input('id'));
    }
}
