<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use App\Models\DeutscheTelekomEmail;
use App\Models\Scopes\PendingCasesScope;
use App\Models\User;
use App\Scopes\CountryScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CaseController extends Controller
{
    public function confirm_pending_case(Request $request)
    {
        $params = $request->query();

        return view('employee.confirm-pending-case', ['params' => $params]);
    }

    public function store_pending_case(Request $request)
    {
        $case = Cases::query()->withoutGlobalScopes([
            PendingCasesScope::class,
            CountryScope::class,
        ])->where('id', $request->get('case'))->first();

        if (! $case) {
            return view('employee.store-pending-case');
        }

        if ((int) $case->company_id !== config('companies.deutsche-telekom') || $case->getRawOriginal('status') !== 'pending') {
            return view('employee.store-pending-case');
        }

        // Add the case to the employee's cases
        $email = DeutscheTelekomEmail::query()
            ->where('email', Str::of((string) $request->get('email'))->lower()->trim())
            ->first();

        DB::transaction(function () use ($case, $email, $request): void {
            if ($email) {
                $email->update([
                    'case_id_2' => $email->case_id_2 ?: $case->id,
                    'case_id_3' => $email->case_id_2 ? $case->id : null,
                ]);
            } else {
                DeutscheTelekomEmail::query()->create([
                    'email' => Str::of((string) $request->get('email'))->lower()->trim(),
                    'case_id_1' => $case->id,
                ]);
            }

            // Update the case status to be available in the dashboard
            $case->update([
                'status' => 'opened',
            ]);

            /**
             * If the case type is not "Psychological", then assign an expert to the case
             *
             * 1 - is the permission id of the "Psychological" case type
             */
            if (optional($case->case_type)->value && (int) optional($case->case_type)->value !== 1) {
                $available_experts = $case->getAvailableExperts();
                if ($available_experts->count() > 0) {
                    $request->merge([
                        'expert_id' => $available_experts->first()->id,
                        'case_id' => $case->id,
                    ]);

                    User::assignCase($request->case_id, $request->expert_id);
                }

            }
        });

        return view('employee.store-pending-case');
    }
}
