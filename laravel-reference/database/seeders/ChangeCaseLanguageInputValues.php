<?php

namespace Database\Seeders;

use App\Models\CaseValues;
use App\Models\RiportValue;
use Illuminate\Database\Seeder;

class ChangeCaseLanguageInputValues extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** Array containing the original case input value
         * and the id reffering to the language skill table */
        $langValues = [
            145 => 15,
            146 => 4,
            147 => 5,
            148 => 1,
            149 => 2,
            150 => 3,
            151 => 7,
            152 => 8,
            153 => 12,
            154 => 10,
            155 => 11,
            250 => 6,
            255 => 14,
            260 => 13,
            278 => 9,
            293 => 16,
            294 => 17,
            295 => 18,
            296 => 19,
            308 => 20,
        ];

        // Update case values
        foreach ($langValues as $original => $new) {
            $inputs = CaseValues::query()->where('case_input_id', 32)->where('value', $original)->get();
            foreach ($inputs as $input) {
                $input->value = (string) $new;
                $input->save();
            }
        }

        // Update riport values
        foreach ($langValues as $original => $new) {
            $inputs = RiportValue::query()->where('type', 32)->where('value', $original)->get();
            foreach ($inputs as $input) {
                $input->value = (string) $new;
                $input->save();
            }
        }
    }
}
