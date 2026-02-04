<?php

namespace App\Console\Commands;

use App\Models\Cases;
use App\Models\CloseTelusCase;
use App\Traits\CaseCloseTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseCloseableTelusCases extends Command
{
    use CaseCloseTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:close-closeable-telus-cases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close telus/lifeworks cases that are closeable 40 days after they were created.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $closeable_cases = CloseTelusCase::query()->with('case')->where('closeable_after', '<=', Carbon::now())->get();

        $closeable_cases->each(function ($item): void {
            if ($item->case) {
                $item->case()->update([
                    'customer_satisfaction' => $this->satisfaction_point() ?? 10,
                    'status' => 'confirmed',
                    'confirmed_at' => Carbon::now('Europe/Budapest'),
                ]);

                if ($item->case->company_id === 1173) {
                    $this->prezero_iberia_customer_satisfaction($item->case);
                }

                // Generate consultations
                if ($item->case->consultations->count() === 0) {
                    $permission = $item->case->company->permissions()->where('permission_id', 1)->first();
                    $consultation_count = match ((int) $permission->getRelationValue('pivot')->number) {
                        5 => 4,
                        3 => 3,
                        default => 4,
                    };

                    if (in_array((int) $item->case->case_type->value, [1, 11])) { // Pshychological or Coaching problem type
                        for ($i = 0; $i < $consultation_count; $i++) {
                            $this->create_consultation($item->case, $i + 1);
                        }
                    } else { // other problem type
                        $this->create_consultation($item->case, 1);
                    }
                }
            }
        });

        $closeable_cases->each->delete();

        return Command::SUCCESS;
    }

    public function create_consultation(Cases $case, int $day): void
    {
        $case->consultations()->create([
            'case_id' => $case->id,
            'user_id' => 829,
            'permission_id' => (int) $case->case_type->value,
            'created_at' => Carbon::now()->startOfMonth()->setDay($day),
        ]);
    }

    public function weighted_random(array $weights): mixed
    {
        $rand = mt_rand(1, array_sum($weights));
        foreach ($weights as $item => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $item;
            }
        }

        return null;
    }

    public function satisfaction_point(): ?int
    {
        return $this->weighted_random([
            10 => 60,
            9 => 30,
            8 => 10,
        ]);
    }
}
