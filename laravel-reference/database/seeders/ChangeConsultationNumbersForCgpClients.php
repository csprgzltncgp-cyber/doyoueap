<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ContractHolder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeConsultationNumbersForCgpClients extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = ContractHolder::query()
            ->where('id', 2) // CGP
            ->first()
            ->companies();

        $companies->each(function (Company $company): void {
            $permission = $company->permissions->where('id', 1)->first(); // Psychological permission

            if ($permission) {
                DB::table('permission_x_company')
                    ->where('company_id', $company->id)
                    ->whereIn('permission_id', [2, 3]) // Legal(2), Financial(3)
                    ->update([
                        'number' => $permission->getRelationValue('pivot')->number, // Set it to the same number as the psychological consultation
                        'updated_at' => Carbon::now(),
                    ]);
            }
        });
    }
}
