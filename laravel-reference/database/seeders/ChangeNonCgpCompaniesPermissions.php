<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class ChangeNonCgpCompaniesPermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Change the 'chat-video-phone-personal' contact value to 'video-phone-personal'
         * for every active company where the contract holder is not CGP.
         */
        Company::query()
            ->whereHas('org_datas', fn ($q) => $q->whereNot('contract_holder_id', 2)) // Not CGP
            ->where('active', 1)
            ->get()->each(function ($company): void {
                $company->permissions()->where('contact', 'chat-video-phone-personal')->each(function ($permission): void {
                    $permission->getRelationValue('pivot')->update(['contact' => 'video-phone-personal']);
                });
            });
    }
}
