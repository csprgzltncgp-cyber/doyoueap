<?php

namespace App\Console\Commands;

use App\Jobs\TranslateFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateFileTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-file-translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate php file content to the specified language. Intended to translate language files used by the dashboard.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $target_lang = 'uk'; // Ukranian language
        $target_file = 'myeap.php';
        $content = File::get(base_path().'/lang/en/'.$target_file);

        if ($content) {
            TranslateFile::dispatch(
                file_name: $target_file,
                file_content: $content,
                language_code: $target_lang
            );
        }
    }
}
