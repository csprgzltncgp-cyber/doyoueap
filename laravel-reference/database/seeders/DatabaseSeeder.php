<?php

namespace Database\Seeders;

use App\Models\CaseValues;
use App\Traits\ContractDateTrait;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class DatabaseSeeder extends Seeder
{
    use ContractDateTrait;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (CaseValues::query()->cursor() as $case_value) {
            try {
                $value = Crypt::decryptString($case_value->value);

                $case_value->update([
                    'value' => $value,
                ]);

                continue;
            } catch (Exception) {

            }

            try {
                $value = Crypt::decrypt($case_value->value);

                $case_value->update([
                    'value' => $value,
                ]);

                continue;
            } catch (Exception) {

            }
        }
    }
}
