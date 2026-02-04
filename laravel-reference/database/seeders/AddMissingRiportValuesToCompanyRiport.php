<?php

namespace Database\Seeders;

use App\Models\Cases;
use App\Models\Riport;
use App\Models\RiportValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AddMissingRiportValuesToCompanyRiport extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $riport_id = null;

        // $company_id => $input_id
        $company_inputs = [
            144 => 71, // Grupa Zywiec Spolka Akcyjna (Heineken Poland), Grupa Żywiec
            168 => 75, // GSK - Eastern Europe
            171 => 77, // Johnson & Johnson
            184 => 76, // Syngenta
            857 => 72, // Robert Bosch & Bosch Rexroth Poland
            1041 => 74, // SK On Hungary Kft.
        ];

        foreach ($company_inputs as $company_id => $missing_input_id) {
            $cases = Cases::query()
                ->where('company_id', $company_id)
                ->whereNotIn('status', ['assigned_to_expert', 'employee_contacted', 'opened'])
                ->get();

            $company_riports = Riport::query()->where('company_id', $company_id)->get();

            $riport_dates = [];
            foreach ($company_riports as $riport) {
                $riport_dates[$riport->id] = [
                    'from' => $riport->from,
                    'to' => $riport->to,
                ];
            }

            foreach ($cases as $case) {
                foreach ($riport_dates as $current_riport_id => $dates) {
                    if (Carbon::parse($case->confirmed_at)->between($dates['from'], $dates['to'])) {
                        $riport_id = $current_riport_id;
                    }
                }
                $data[$company_id][$case->id] = $riport_id;
                $this->generate_riport_values($case, $riport_id, $missing_input_id);
            }
        }
    }

    private function generate_riport_values($case, int|string|null $riport_id, int $missing_input_id): void
    {
        foreach ($case->values()->get() as $value) {
            if (! $value->value) {
                continue;
            }
            if ($value->case_input_id != $missing_input_id) {
                continue;
            }
            RiportValue::query()->create([
                'type' => $value->case_input_id,
                'value' => $value->value,
                'country_id' => $case->country_id,
                'riport_id' => $riport_id,
                'connection_id' => null,
            ]);
        }
    }
}
