<?php

namespace App\Console\Commands;

use App\Enums\CaseExpertStatus;
use App\Models\Cases;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ConfirmCasesAfterOneMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:confirm-after-1-month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If case is confirmable but not confirmed within 1 month confirm it.';

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
    public function handle(): int
    {
        Log::info('[COMMAND][ConfirmCasesAfterOneMonth]: fired!');

        $confirmed_cases = collect([]);
        $cases = Cases::query()
            ->whereIn('status', ['assigned_to_expert', 'employee_contacted'])
            ->whereNot('company_id', 843) // Kivesszük az LPP céges(843) eseteket a listából mert náluk az elégedettségi pontszám kitöltése nem szükséges, ezért akkor is lezárná az eseteket amikor még nem kéne.
            ->get();

        foreach ($cases as $case) {
            $expert = User::query()
                ->where('type', 'expert')
                ->whereHas('cases', fn (Builder $query) => $query->where('case_id', $case->id)->where('accepted', CaseExpertStatus::ACCEPTED->value))->first();

            if ($case->isCloseable($expert)['closeable'] && Carbon::parse($case->updated_at)->lt(Carbon::now()->subMonthWithNoOverflow())) {
                $case->update([
                    'confirmed_by' => $expert->id,
                    'closed_by_expert' => $expert->id,
                    'confirmed_at' => Carbon::now('Europe/Budapest'),
                    'status' => 'confirmed',
                ]);

                $confirmed_cases->push($case->id);
            }
        }

        Log::info('[COMMAND][ConfirmCasesAfterOneMonth]: Confirmed cases with id(s) of: '.$confirmed_cases);

        return self::SUCCESS;
    }
}
