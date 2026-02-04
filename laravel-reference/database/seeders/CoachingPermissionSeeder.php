<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ContractHolder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoachingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = ContractHolder::query()
            ->where('id', 2) // CGP Europe
            ->first()
            ->companies();

        $companies->each(function (Company $company): void {
            $permission = $company->permissions->where('id', 1)->first(); // Psychological permission

            if ($permission) {
                DB::table('permission_x_company')->insert([
                    'permission_id' => 11, // Coaching
                    'company_id' => $company->id,
                    'number' => $permission->getRelationValue('pivot')->number,
                    'duration' => $permission->getRelationValue('pivot')->duration,
                    'contact' => $permission->getRelationValue('pivot')->contact,
                ]);
            }
        });
    }
}
