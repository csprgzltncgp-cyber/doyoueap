<?php

namespace App\Console\Commands;

use App\Models\Cases;
use App\Models\InvoiceCaseData;
use App\Traits\EapOnline\OnlineTherapyTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseCasesWith6OrLessConsultation extends Command
{
    use OnlineTherapyTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:close-cases-with6-or-less-consultation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close cases where the number of consultations is equal or less than 6 by case identifier.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('[COMMAND][CloseCasesWith6OrLessConsultation]: fired!');

        $case_identifiers = [
            73644873,
            23057206,
            57403756,
            51371304,
            67471305,
            89823749,
            39307548,
            48077147,
            78010217,
            29434233,
            45651856,
            35742614,
            36459896,
            37111342,
            99003996,
            25558756,
            55761084,
            23731797,
            74773336,
            98133978,
            68279789,
            41779085,
            63897580,
            77818473,
            24852302,
            21274251,
            73160837,
            27969020,
            32336994,
            49813496,
            69610476,
            49746941,
            39954642,
            50375036,
            34842833,
            77222045,
            43319388,
            56453397,
            73424994,
            34119569,
            91084650,
            97689453,
            29965561,
            12757444,
            41474051,
            90636010,
            38522251,
            31600031,
            91139579,
            13884129,
            52528208,
            49073372,
            92901120,
            17511211,
            87391447,
            67985418,
            93686460,
            48451163,
            95515284,
            86422125,
            19906044,
            36722664,
            85703098,
            12934833,
            98401163,
            94015574,
            61728567,
            61744652,
            91865369,
            78929195,
            12352318,
            90294764,
            77025666,
            94587266,
            79333611,
            41980374,
            29228981,
            95154114,
            75455660,
            97137610,
            85715524,
            90687329,
            82769591,
            18697688,
            61729155,
            68551198,
            48215534,
            73790281,
            13756511,
            69163551,
            12005973,
            26688939,
            86664429,
            38947857,
            97131674,
            90662907,
            42803946,
            62899015,
            73856684,
            50131707,
            20270983,
            86674421,
            68669613,
            69779269,
            86704081,
            25920653,
            68826904,
            53464698,
            18439165,
            83354359,
            42035870,
            29793193,
            59344260,
            40643792,
            64356357,
            27812086,
            27068737,
            40757293,
            19083944,
            79840140,
            25676334,
            31007964,
            18012278,
            51338094,
            92668445,
            80053771,
            89879770,
            28616405,
            71944658,
            49895859,
            83199234,
            12842161,
            14467701,
            29679772,
            27027726,
            65242182,
            69151915,
            41484852,
            97449812,
            20211076,
            36063774,
            17722601,
            11315435,
            75118765,
            27466884,
            13054594,
            19041435,
            55027821,
            33428737,
            50823402,
            56640312,
            49394335,
            21112965,
            64143694,
            52033944,
            79428418,
            42458131,
            39628165,
            98351723,
            19234934,
            10682848,
            25787345,
            63922333,
            55698339,
            47892106,
            51559320,
            61484767,
            78233656,
            23905253,
            35572096,
            75533414,
            87825931,
            11179875,
            13722417,
            57174740,
            34013432,
            96404284,
            97872238,
            20662556,
            21133234,
            85498271,
            33925180,
            47523315,
            72899211,
            46178743,
            72213181,
            61853018,
            21932764,
            48988500,
            18587951,
            51812549,
            43305012,
            83688617,
            43843586,
            17653846,
            89560766,
            60385217,
            54718798,
            33828791,
            75273188,
            67552610,
            55315135,
            43887819,
        ];

        collect($case_identifiers)->each(function (int $case_identifier): void {
            $case = Cases::query()->where('case_identifier', $case_identifier)->first();

            if ($case && in_array($case->getRawOriginal('status'), ['employee_contacted', 'assigned_to_expert', 'opened'])) {
                try {
                    $expert = $case->case_accepted_expert();

                    // IF company permission for the problem type is 6 or less
                    if ($case->company->permissions->where('id', (int) $case->case_type->value)->first()->getRelationValue('pivot')->number <= 6) {
                        $case->update([
                            'closed_by_expert' => $expert->id,
                            'status' => 'confirmed',
                            'confirmed_by' => $expert->id,
                            'confirmed_at' => Carbon::now('Europe/Budapest'),
                            'updated_at' => Carbon::now('Europe/Budapest'),
                        ]);

                        if (! $case->customer_satisfaction) {
                            $case->customer_satisfaction = 10;
                            $case->save();
                        }

                        $this->exclude_client_from_online_therapy($case->id);
                        $this->set_intake_colsed_at_date($case->id);

                        $permission_id = optional($case->case_type)->value;
                        $duration = $permission_id ? optional(optional($case->company->permissions()->where('permissions.id', $permission_id)->first())->pivot)->duration : null;

                        InvoiceCaseData::query()->firstOrCreate([
                            'case_identifier' => $case->case_identifier,
                            'consultations_count' => $case->consultations->count(),
                            'expert_id' => $expert->id,
                            'duration' => (int) $duration,
                        ]);
                    }

                    $this->exclude_client_from_online_therapy($case->id);
                    $this->set_intake_colsed_at_date($case->id);

                    $duration = optional($case->values->where('case_input_id', 22)->first())->input_value->value;

                    InvoiceCaseData::query()->firstOrCreate([
                        'case_identifier' => $case->case_identifier,
                        'consultations_count' => $case->consultations->count(),
                        'expert_id' => $expert->id,
                        'duration' => (int) $duration,
                        'permission_id' => (int) $case->case_type->value,
                    ]);
                } catch (Exception $e) {
                    Log::info("[COMMAND][CloseCasesWith6OrLessConsultation]: Failed! Case identifier: {$case_identifier}. ERROR: {$e->getMessage()}");
                }
            }
        });
    }
}
