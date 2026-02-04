<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ContractHolder;
use App\Models\Permission;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddWellBeingCoachingPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::query()->create([
            'id' => 16,
            'slug' => 'Well Being Coaching',
        ]);

        foreach ([1, 2, 3, 4, 5] as $lang_id) {
            Translation::query()->create([
                'value' => 'Well Being Coaching',
                'language_id' => $lang_id,
                'translatable_id' => 16,
                'translatable_type' => Permission::class,
            ]);
        }

        $companies = ContractHolder::query()
            ->where('id', 3) // Compsych
            ->first()
            ->companies();

        $companies->each(function (Company $company): void {
            $permission = $company->permissions->where('id', 1)->first(); // Psychological permission

            if ($permission) {
                DB::table('permission_x_company')->insert([
                    'permission_id' => 16, // Well Being Coaching
                    'company_id' => $company->id,
                    'number' => $permission->getRelationValue('pivot')->number,
                    'duration' => $permission->getRelationValue('pivot')->duration,
                    'contact' => $permission->getRelationValue('pivot')->contact,
                ]);
            }
        });
    }
}
