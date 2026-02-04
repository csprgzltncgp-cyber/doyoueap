<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class TranslateFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        public string $file_name,
        public string $file_content,
        public string $language_code)
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
                'max_tokens' => 12000,
                'temperature' => 0.3,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
                'top_p' => 1,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "
                            Translate the values in this php array \"{$this->file_content}\" from english(en) into {$this->language_code}. \n
                            Only translate the values and leave the array keys intact. \n\n
                            IMPORTANT: Some of the values might be nested arrays. In this case translate the values inside the nested array. \n
                            RETURN ONLY THE CONTENT GIVEN WITH THE TRANLATION AND NOTHING ELSE \n
                            ENSURE THE CONTENT BEGINS WITH THE PROPER PHP OPENING TAG <?php \n
                            ENSURE THERE ARE NO UNNECESSARY CHARACTERS AT THE START OR END OF THE CONTENT LIKE '```php' or '```'
                            THE RETURNED CONTENT WILL BE INSIERTED INTO A NEW PHP FILE. \n
                        ",
                    ],
                    [
                        'role' => 'user',
                        'content' => "Translate the following text: \"{$this->file_content}\"",
                    ],
                ],
            ]);

            $translated_content = $result->choices[0]->message->content;
            File::put(base_path().'/lang/uk/'.$this->file_name, $translated_content);

        } catch (Throwable $th) {
            if ($this->attempts() > $this->tries - 1) {
                Log::info("Translation with id of {$this->file_content} failed after {$this->tries} attempts!");
                throw $th;
            }

            $this->release($this->timeout);

            return;
        }
    }
}
