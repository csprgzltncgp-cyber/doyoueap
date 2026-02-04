<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ContractHolder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EapMenuPermissionSeeder extends Seeder
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

        $menus = [
            19, // Live Webinar
        ];

        $company_menu_items = DB::connection('mysql_eap_online')->table('company_menu_item');

        $companies->each(function (Company $company) use ($menus, $company_menu_items): void {
            collect($menus)->each(function (int $menu_id) use ($company, $company_menu_items): void {
                $exists = $company_menu_items
                    ->where('company_id', $company->id)
                    ->where('menu_item_id', $menu_id)
                    ->exists();

                if (! $exists) {
                    $company_menu_items->insert([
                        'company_id' => $company->id,
                        'menu_item_id' => $menu_id,
                    ]);
                }
            });
        });
    }
}
