<?php

namespace App\Traits\Prizegame;

use App\Models\PrizeGame\Content;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ContentTrait
{
    public function recreate_content($content_id, $company_id = null, $country_id = null, $language_id = null)
    {
        $content = Content::query()->findOrFail($content_id);
        $content->load(['sections', 'questions', 'answers', 'image', 'digits']);

        // Content
        $new_content = Content::query()->create([
            'language_id' => $content->language_id,
            'type_id' => $content->type_id,
            'company_id' => $company_id,
            'country_id' => $country_id,
        ]);

        // Sections and documents
        foreach ($content->sections as $section) {
            $new_section = $new_content->sections()->save($section->replicate(['content_id']));

            $new_section->translations()->create([
                'language_id' => $language_id ?? $content->language_id,
                'value' => $section->get_translation($content->language),
            ]);

            if ($section->documents) {
                $new_document = $section->documents->replicate(['section_id']);

                $extension = strrchr((string) $section->documents->filename, '.');
                $new_filename = time().'-'.Str::random(10).$extension;
                try {
                    Storage::copy('eap-online/prizegame/documents/'.$section->documents->filename, 'eap-online/prizegame/documents/'.$new_filename);
                } catch (Exception $e) {
                    Log::error('Error copying image: '.$e->getMessage());
                }
                $new_document->filename = $new_filename;
                $new_section->save();
                $new_document = $new_section->documents()->create($new_document->toArray());

                $new_document->translations()->create([
                    'language_id' => $language_id ?? $content->language_id,
                    'value' => $section->documents->get_translation($content->language),
                ]);
            }
        }

        // Questions, Answers and digits for questions
        foreach ($content->questions as $question) {
            $replicated_question = $question->replicate(['content_id']);
            $new_question = $new_content->questions()->save($replicated_question);

            $new_question->translations()->create([
                'language_id' => $language_id ?? $content->language_id,
                'value' => $question->get_translation($content->language),
            ]);

            foreach ($question->answers as $answer) {
                $replicated_answer = $answer->replicate(['question_id']);
                $new_answer = $new_question->answers()->save($replicated_answer);

                $new_answer->translations()->create([
                    'language_id' => $language_id ?? $content->language_id,
                    'value' => $answer->get_translation($content->language),
                ]);
            }

            if ($question->digit) {
                $new_digit = $question->digit->replicate(['content_id', 'question_id']);
                $new_digit->question_id = $new_question->id;
                $new_content->digits()->save($new_digit);
            }
        }

        // Digits with no questions
        foreach ($content->digits as $digit) {
            if (empty($digit->question_id)) {
                $new_digit = $digit->replicate(['content_id']);
                $new_content->digits()->save($new_digit);
            }
        }

        // Background image
        if ($content->image != null) {
            $new_image = $content->image->replicate(['content_id']);
            $extension = strrchr((string) $content->image->filename, '.');
            $new_filename = time().'-'.Str::random(10).$extension;
            try {
                Storage::copy('eap-online/prizegame/images/'.$content->image->filename, 'eap-online/prizegame/images/'.$new_filename);
            } catch (Exception $e) {
                Log::error('Error copying image: '.$e->getMessage());
            }
            $new_image->filename = $new_filename;
            $new_content->image()->save($new_image);
        }

        return $new_content->id;
    }
}
