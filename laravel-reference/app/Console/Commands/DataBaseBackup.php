<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DataBaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup and save it on the filesystem.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][DataBaseBackup]: fired!');

        // File
        $prefix = 'operator-';
        $extension = '.sql.gz';
        $filename = $prefix.now()->format('Y-m-d').$extension;

        // Local
        $local_path = storage_path().'/app/backup/';

        // Dropbox
        $dropbox_client = new Client(config('filesystems.disks.dropbox.token'));
        $dropbox_adapter = new DropboxAdapter($dropbox_client);
        $dropbox_filesystem = new Filesystem($dropbox_adapter, ['case_sensitive' => false]);
        $dropbox_path = '/cgp_europe/operator/';

        // create backup directory if not exist
        if (! File::isDirectory($local_path)) {
            File::makeDirectory($local_path, 0777, true, true);
        }

        // deleting backups that older than a week
        collect(File::allFiles($local_path))->filter(fn ($file) => now()->subWeek()->gt(Carbon::parse($this->string_between($file->getFilename(), $prefix, $extension))))->each(function ($file) use ($dropbox_path, $dropbox_filesystem): void {
            if (File::exists($file)) {
                File::delete($file);
                Log::info('[COMMAND][DataBaseBackup]: Local backup deleted with name of: '.$file->getFilename().'!');
            }

            if ($dropbox_filesystem->has($dropbox_path.$file->getFilename())) {
                $dropbox_filesystem->delete($dropbox_path.$file->getFilename());
                Log::info('[COMMAND][DataBaseBackup]: Dropbox backup deleted with name of: '.$file->getFilename().'!');
            }
        });

        $command = 'mysqldump --user='.config('database.connections.mysql.username').
            ' --password='.config('database.connections.mysql.password').
            ' --host='.config('database.connections.mysql.host').' '
            .config('database.connections.mysql.database').'  | gzip > '.$local_path.$filename;

        $return_val = null;
        $output = null;

        exec($command, $return_val, $output);

        if (! $dropbox_filesystem->has($dropbox_path.$filename)) {
            $dropbox_filesystem->write($dropbox_path.$filename, file_get_contents(storage_path().'/app/backup/'.$filename));
        }

        Log::info('[COMMAND][DataBaseBackup]: backup created with name of: '.$filename.'!');
    }

    private function string_between($str, string $starting_word, string $ending_word): string
    {
        $subtring_start = strpos((string) $str, $starting_word);
        $subtring_start += strlen($starting_word);
        $size = strpos((string) $str, $ending_word, $subtring_start) - $subtring_start;

        return substr((string) $str, $subtring_start, $size);
    }
}
