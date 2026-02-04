<?php

namespace App\Jobs\EapOnline;

use App\Models\EapOnline\EapLanguageLines;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Laravel\Facades\OpenAI;

class TranslateLanguageLine implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;

    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public EapLanguageLines $languageLine, public string $from, public string $to) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->languageLine->text[$this->from])) {
            $text_to_translate = $this->languageLine->text['hu'];
        } else {
            $text_to_translate = $this->languageLine->text[$this->from];
        }

        if (empty($text_to_translate)) {
            return;
        }

        if (! empty($this->languageLine->text[$this->to])) {
            return;
        }

        $result = OpenAi::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => "Translate the following text from {$this->from} to {$this->to}: \"{$text_to_translate}\"",
            'max_tokens' => 2000,
            'temperature' => 0.3,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $translated_text = $result['choices'][0]['text'];

        $translations = $this->languageLine->text;
        $translations[$this->to] = str_replace('"', '', str_replace("\n", '', $translated_text));
        $this->languageLine->text = $translations;
        $this->languageLine->save();
    }
}
