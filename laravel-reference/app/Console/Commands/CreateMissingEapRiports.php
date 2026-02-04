<?php

namespace App\Console\Commands;

use App\Jobs\CreateEapRiportForCompany;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CreateMissingEapRiports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eap-riports:create-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
        Create eap riports for specific companies and quarters,
        when for some reason there are missing company eap riports.
    ';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][CreateEapRiports]: fired!');

        $company_id = [
            // 1029, // Lidl Eesti OÜ
            // 988, // Lidl Latvia
            // 966, // Lidl Czech Republic
            953, // VALEO LIGHTING INJECTION SA
            964, // Rossmann Magyarország Kft.
            989, // DHL Parcel
            991, // Telekom Digitalizációs Tanácsadás
            992, // Grupa Zywiec (Grupa Żywiec) - Leżajsk / Lezajsk
            996, // Inditex/Zara/Zara Home/Pull&Bear/Stradivarius/Bershka/Oysho/Massimo Dutti
            998, // Robert Bosch Elektonika Kft. Hatvan
            999, // KUKA HUNGÁRIA Kft.
            1001, // MAHLE COMPONENTE DE MOTOR SRL
            1024, // Lucky 7/ Lucky7
            1028, // Apollo Tyres Kft.
            1041, // Empteezy
            1043, // SK On Hungary Kft.
            1040, // Provident Pénzügyi Zrt. (Gyémánt partneri program)
            1054, // Allianz Technology SE München Sucursala București
            1064, // Valeo Detection System s.r.o
            1070, // CPL Jobs Sp. z o.o.
            1072, // Kühne + Nagel Szállítmányozási Kft.
            1079, // Esky
        ];
        $quarters = [3, 2, 1];

        foreach ($quarters as $quarter) {
            $from = Carbon::now()->subDay()->subQuarters($quarter)->startOfQuarter()->format('Y-m-d');
            $to = Carbon::now()->subDay()->subQuarters($quarter)->endOfQuarter()->format('Y-m-d');

            Company::query()
                ->whereIn('id', $company_id)
                ->get()->map(function ($company) use ($from, $to): void {
                    CreateEapRiportForCompany::dispatch($company, $from, $to);
                });
        }

        return Command::SUCCESS;
    }
}
