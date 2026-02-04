<?php

namespace App\Jobs\EapOnline;

use App\Models\EapOnline\EapTranslation;
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

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public EapTranslation $translation,
        public string $to,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $result = OpenAi::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => "Translate the following text from english to Albanian: \"{$this->translation->value}\"",
                'max_tokens' => 2000,
                'temperature' => 0.3,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);

            $translated_text = $result['choices'][0]['text'];

            EapTranslation::query()->create([
                'translatable_id' => $this->translation->translatable_id,
                'translatable_type' => $this->translation->translatable_type,
                'language_id' => $this->to,
                'value' => str_replace('"', '', str_replace("\n", '', $translated_text)),
            ]);
        } catch (Throwable $th) {
            if ($this->attempts() > 2) {
                Log::info("Translation with id of {$this->translation->id} failed after 3 attempts!");
                throw $th;
            }

            $this->release(120);

            return;
        }
    }
}
