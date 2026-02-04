<?php

namespace App\Imports;

use App\Enums\ContractHolderEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompaniesImport implements ToModel, WithHeadingRow
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        $country_id = 1;

        $new_company = Company::query()->create([
            'name' => $row['company_name'],
            'customer_satisfaction_index' => 0,
            'eap_online_riport' => 0,
            'active' => 1,
        ]);

        if ($new_company->id) {
            $new_company->countries()->attach($country_id); // Attach country

            $new_company->org_datas()->create([
                'contract_holder_id' => ContractHolderEnum::VPO_TELUS, // VPO/Telus
                'head_count' => $row['current_population'],
                'company_id' => $new_company->id,
                'country_id' => $country_id,
            ]);

            DB::table('permission_x_company')
                ->insert([
                    'permission_id' => 1,
                    'company_id' => $new_company->id,
                    'contact' => 'video-phone-personal',
                    'number' => $row['partner_sessionmodel'] ?? 0,
                    'duration' => 50,
                ]);
        }

        return $new_company;
    }
}
