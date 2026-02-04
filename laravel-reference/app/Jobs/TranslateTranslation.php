<?php

namespace App\Jobs;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class TranslateTranslation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int
     */
    public $tries;

    /**
     * @var int
     */
    public $timeout;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Translation $translation,
        public Language $to)
    {
        $this->tries = app()->environment('production') ? 3 : 1;
        $this->timeout = app()->environment('production') ? 120 : 0;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'max_tokens' => 4096,
                'temperature' => 0.3,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
                'top_p' => 1,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "
                            You are a professional translator. Your job is to translate the given text into {$this->to->code}. \n
                            You are only allowed to return the translated text and nothing else. \n\n
                            IMPORTANT: ONLY RETURN TRANSLATED TEXT AND NOTHING ELSE. \n
                        ",
                    ],
                    [
                        'role' => 'user',
                        'content' => "Translate the following text: \"{$this->translation->value}\"",
                    ],
                ],
            ]);

            $translated_text = $result->choices[0]->message->content;

            Translation::query()->create([
                'translatable_type' => $this->translation->translatable_type,
                'language_id' => $this->to->id,
                'translatable_id' => $this->translation->translatable_id,
                'value' => $translated_text,
            ]);

        } catch (Throwable $th) {
            if ($this->attempts() > $this->tries - 1) {
                Log::info("Translation with id of {$this->translation->id} failed after {$this->tries} attempts!");
                throw $th;
            }

            $this->release($this->timeout);

            return;
        }
    }
}
