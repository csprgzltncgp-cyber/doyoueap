<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Country;
use App\Models\OrgData;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetachTelusCompanyCountries extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries_list = [
            1,
            2,
        ];

        $problematicCompanies = collect([]);

        Company::query()
            ->with(['org_datas'])
            ->whereHas('org_datas', fn ($query) => $query->whereIn('country_id', $countries_list)->where('contract_holder_id', 1))
            ->where('active', 1)
            ->get()->each(function (Company $company) use ($problematicCompanies): void {
                $countries = $company->countries()->pluck('id')->sort()->toArray();

                if (! $countries) {
                    return;
                }

                if (count($countries) == 1) {
                    $company->org_datas()->first()
                        ->update([
                            'contract_holder_id' => 6,
                        ]);
                    DB::table('permission_x_company')->updateOrInsert([
                        'permission_id' => 17,
                        'company_id' => $company->id,
                    ], [
                        'number' => 1,
                        'duration' => 50,
                        'contact' => 'phone',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->syncCompanyOrgDatasWithCountries($company);
                } elseif (count($countries) == 2 && $countries[0] == 1 && $countries[1] == 2) {
                    $company->org_datas()->each(function (OrgData $org_data): void {
                        $org_data->update([
                            'contract_holder_id' => 6,
                        ]);
                    });
                    DB::table('permission_x_company')->updateOrInsert([
                        'permission_id' => 17,
                        'company_id' => $company->id,
                    ], [
                        'number' => 1,
                        'duration' => 50,
                        'contact' => 'phone',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->syncCompanyOrgDatasWithCountries($company);
                } else {
                    $problematicCompanies->push($company);
                }
            });

        $problematicCompanies->each(function (Company $current_company): void {
            $wpo = collect([]);
            $telus = collect([]);

            $countries = $current_company->countries()->orderBy('id')->get();

            $countries->each(function (Country $country) use (&$wpo, &$telus): void {
                switch ($country->id) {
                    case 1:
                        $wpo->push($country);
                        break;
                    case 2:
                        $wpo->push($country);
                    case 5:
                    case 9:
                        $telus->push($country);
                        break;
                    default:
                        throw new Exception('Invalid country id');
                }
            });

            if ($wpo->count() === 0) {
                return;
            }

            $current_permissions = $current_company->permissions()->get();

            $new_company = Company::query()->create([
                'name' => $current_company->name.'/ WPO',
                'orgId' => $current_company->orgId,
                'customer_satisfaction_index' => $current_company->customer_satisfaction_index,
                'eap_online_riport' => $current_company->eap_online_riport,
                'active' => 1,
            ]);

            foreach ($wpo as $country) {
                DB::table('org_data')->insert([
                    'contract_holder_id' => 6,
                    'org_id' => $current_company->org_datas()->where('country_id', $country->id)->first()->org_id,
                    'head_count' => $current_company->org_datas()->where('country_id', $country->id)->first()->head_count,
                    'contract_date' => $current_company->org_datas()->where('country_id', $country->id)->first()->contract_date,
                    'company_id' => $new_company->id,
                    'country_id' => $country->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($current_permissions as $permission) {
                DB::table('permission_x_company')->insert([
                    'permission_id' => $permission->getRelationValue('pivot')->permission_id,
                    'company_id' => $new_company->id,
                    'number' => $permission->getRelationValue('pivot')->number,
                    'duration' => $permission->getRelationValue('pivot')->duration,
                    'contact' => $permission->getRelationValue('pivot')->contact,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('permission_x_company')->updateOrInsert([
                'permission_id' => 17,
                'company_id' => $new_company->id,
            ], [
                'number' => 1,
                'duration' => 50,
                'contact' => 'phone',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $current_company->countries()->detach($wpo->pluck('id'));
            $new_company->countries()->attach($wpo->pluck('id'));

            $this->syncCompanyOrgDatasWithCountries($current_company);
            $this->syncCompanyOrgDatasWithCountries($new_company);
        });
    }

    private function syncCompanyOrgDatasWithCountries(Company $company): void
    {
        $org_datas_to_delete = collect([]);

        $company->org_datas()->each(function (OrgData $org_data) use ($company, $org_datas_to_delete): void {
            if (! $company->countries()->where('id', $org_data->country_id)->exists()) {
                $org_datas_to_delete->push($org_data);
            }
        });

        OrgData::query()->whereIn('id', $org_datas_to_delete->pluck('id')->toArray())->delete();
    }
}
