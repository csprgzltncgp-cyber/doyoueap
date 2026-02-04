<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSingleSessionPermissionToTelusWpoCompanies extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 6)) // Telus/Wpo
            ->get()->each(function (Company $company): void {
                DB::table('permission_x_company')
                    ->insert([
                        'permission_id' => 17,
                        'company_id' => $company->id,
                        'contact' => 'phone',
                        'number' => 1,
                        'duration' => 50,
                    ]);
            });
    }
}
