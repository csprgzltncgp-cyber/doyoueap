<?php

namespace Database\Seeders;

use App\Models\Cases;
use App\Models\InvoiceCaseData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceCaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = Cases::query()
            ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
            ->whereBetween('confirmed_at', [
                Carbon::now()->firstOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->get();

        $cases->map(function ($case): void {
            $closed_by = User::query()->where('id', $case->confirmed_by)->first();
            if (! $closed_by) {
                return;
            }
            if ($closed_by->type != 'expert') {
                return;
            }
            InvoiceCaseData::query()->create([
                'case_identifier' => $case->case_identifier,
                'consultations_count' => $case->consultations->count(),
                'expert_id' => $closed_by->id,
            ]);
        });
    }
}
