<?php

namespace Database\Seeders;

use App\Models\CrisisCase;
use App\Models\Riport;
use App\Models\RiportValue;
use App\Models\WorkshopCase;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CreateMissingWorkshopAndCrisisRiportData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RiportValue::query()->where('type', RiportValue::TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS)->delete();

        $from = Carbon::parse('2022-01-01');
        $to = Carbon::parse('2022-09-01');

        WorkshopCase::query()
            ->whereBetween('date', [$from, $to])
            ->where('status', 3)
            ->get()
            ->map(function ($workshop): void {
                if (empty($workshop->number_of_participants)) {
                    $workshop->number_of_participants = random_int(15, 20);
                    $workshop->save();
                }

                $riport = Riport::query()->where([
                    'company_id' => $workshop->company_id,
                    'from' => Carbon::parse($workshop->date)->startOfMonth()->format('Y-m-d'),
                    'to' => Carbon::parse($workshop->date)->endOfMonth()->format('Y-m-d'),
                ])->first();

                if ($riport) {
                    RiportValue::query()->create([
                        'type' => RiportValue::TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS,
                        'value' => $workshop->number_of_participants,
                        'country_id' => $workshop->country_id,
                        'riport_id' => $riport->id,
                    ]);
                }
            });

        CrisisCase::query()
            ->whereBetween('date', [$from, $to])
            ->where('status', 3)
            ->whereNull('number_of_participants')
            ->get()
            ->map(function ($crisis): void {
                $number_of_participants = random_int(5, 10);
                $crisis->number_of_participants = $number_of_participants;
                $crisis->save();

                $riport = Riport::query()->where([
                    'company_id' => $crisis->company_id,
                    'from' => Carbon::parse($crisis->date)->startOfMonth()->format('Y-m-d'),
                    'to' => Carbon::parse($crisis->date)->endOfMonth()->format('Y-m-d'),
                ])->first();

                if ($riport) {
                    RiportValue::query()->create([
                        'type' => RiportValue::TYPE_CRISIS_NUMBER_OF_PARTICIPANTS,
                        'value' => $crisis->number_of_participants,
                        'country_id' => $crisis->country_id,
                        'riport_id' => $riport->id,
                    ]);
                }
            });
    }
}
