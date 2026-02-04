<?php

namespace Database\Seeders;

use App\Enums\CompanyContactPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddContactPermissionToCompany extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company_ids = collect([
            1173, // PreZero Iberia , S.L.
            1097, // Lidl Portugal
            1172, // PreZero Portugal, S. A.
            1130, // PreZero Stiftung & Co. KG,
            1179, // PreZero Deutschland KG
            1126, //  Mirum Pharma
            566, // Graphisoft
            1133, // PIB GROUP SERVICES Ltd.
            1095, // ASTRAZENECA BALKAN /UK LIMITED, PODRUŽNICA V SLOVENIJI
            639, // Kiwi.com s.r.o.
            1024, // Lucky 7/ Lucky7
            717, // Superbet Betting & Gaming SA,
            44, // Worldquant
        ]);

        $permission_ids = collect([
            1, // Psychological
        ]);

        $contact = CompanyContactPermission::TYPE_7->value; // Which consultation contact will be available for the company

        $number = 5; // Number of available consultation

        $duration = 50; // Duration of the consultation

        $company_ids->each(function ($company_id) use ($permission_ids, $contact, $number, $duration): void {
            $permissions = DB::table('permission_x_company')
                ->where('company_id', $company_id)
                ->get();
            $permission_ids->each(function ($permission_id) use ($permissions, $company_id, $contact, $number, $duration): void {
                if (! $permissions->where('permission_id', $permission_id)->first()) {
                    DB::table('permission_x_company')
                        ->insert([
                            'permission_id' => $permission_id,
                            'company_id' => $company_id,
                            'contact' => $contact,
                            'number' => $number,
                            'duration' => $duration,
                        ]);
                } else { // IF permission exists but with a different contact, update the contact to the new one
                    DB::table('permission_x_company')
                        ->where('permission_id', $permission_id)
                        ->where('company_id', $company_id)
                        ->update(['contact' => $contact]);
                }
            });
        });
    }
}
