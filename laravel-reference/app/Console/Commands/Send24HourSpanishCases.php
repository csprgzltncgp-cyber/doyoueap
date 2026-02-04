<?php

namespace App\Console\Commands;

use App\Exports\TwentyFourHourSpanishCasesExport;
use App\Mail\Send24HourSpanishCasesEmail;
use App\Models\Cases;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class Send24HourSpanishCases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send24-hour-spanish-cases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with an attached excel containing a list of Spanish cases created in the last 24 hour';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][Send24HourSpanishCases]: fired!');

        $today = Carbon::now();

        $from = $today->copy()->startOfWeek();
        $to = $today->copy()->endOfDay();

        $cases = Cases::query()
            ->whereBetween('created_at', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')])
            ->where('country_id', 43) // 43 = Spain
            ->get();

        if (! $cases->isEmpty()) {
            Excel::store(new TwentyFourHourSpanishCasesExport($cases), 'spanish_cases_'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx', 'private');
            $file_path = storage_path('app/spanish_cases_'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.xlsx');
            Mail::to('anita.tompa@cgpeu.com')->send(new Send24HourSpanishCasesEmail($file_path, $from, $to));

            // Remove temp excel file
            if (File::exists($file_path)) {
                File::delete($file_path);
            }
        }
    }
}
