<?php

namespace App\Console\Commands;

use App\Exports\CompanyUtilizationExport;
use App\Mail\CompanyUtilizationMail;
use App\Models\Company;
use App\Models\Riport;
use App\Models\RiportValue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendCompanyUtilizationMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-company-utilization-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
        Send email to Anita Tompa, Barbara Kiss and Peter Janky about companies whose total utilization 
        from the start of the year until the end of last month surpases 5%
    ';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $companies = Company::query()
            ->where('active', 1)
            ->with('riports', 'org_datas', 'countries')
            ->whereHas('org_datas', function ($q): void {
                $q->where('contract_holder_id', 2); // CGP
                $q->whereNotNull('head_count');
            })
            ->whereHas('riports', function ($q): void {
                $q->where('from', '>=', Carbon::now()->subMonthNoOverflow()->startOfYear());
                $q->where('to', '<=', Carbon::now()->subMonthNoOverflow()->endOfMonth());
            })
            ->cursor();

        $data = [];

        $companies->each(function (Company $company) use (&$data): void {
            $data[$company->id] = [];

            $company->countries->each(function ($country) use ($company, &$data): void {
                $data[$company->id][$country->name] = [
                    'head_count' => 0,
                    'case_count' => 0,
                    'utilization' => 0,
                    'reported_months' => $company->riports()
                        ->where('from', '>=', Carbon::now()->subMonthWithNoOverflow()->startOfYear())
                        ->where('to', '<=', Carbon::now()->subMonthWithNoOverflow()->endOfMonth())
                        ->count(),
                    'missing_months' => 0,
                    'original_case_count' => 0,
                ];
            });

            // Get headcounts and case numbers per country
            $company->riports
                ->where('from', '>=', Carbon::now()->subMonthWithNoOverflow()->startOfYear())
                ->where('to', '<=', Carbon::now()->subMonthWithNoOverflow()->endOfMonth())
                ->each(function (Riport $riport) use ($company, &$data): void {
                    $company->countries->each(function ($country) use ($company, $riport, &$data): void {
                        $headcount = 0;
                        if (! $company->org_datas->isEmpty()) {
                            $headcount = (int) $company->org_datas->where('country_id', $country->id)->first()->head_count;
                        }

                        $data[$company->id][$country->name]['head_count'] = $headcount;
                        $data[$company->id][$country->name]['case_count'] += $riport
                            ->values
                            ->where('country_id', $country->id)
                            ->where('type', RiportValue::TYPE_STATUS)
                            ->whereIn('value', ['confirmed', 'interrupted', 'interrupted_confirmed', 'client_unreachable', 'client_unreachable_confirmed'])
                            ->count();
                    });
                });

            // Unset company relations to free up memory
            $company->unsetRelation('riports');
            $company->unsetRelation('org_datas');
            $company->unsetRelation('countries');
        });

        // Calculate utilization per country
        $data = collect($data)->map(fn ($countries) => collect($countries)->map(function (array $country): array {

            $country['missing_months'] = 12 - $country['reported_months'];
            $country['original_case_count'] = $country['case_count'];

            /*
             * If the the headcount is 0 return 0 utilization
             */
            if ($country['head_count'] === 0) {
                $country['utilization'] = 0;

                return $country;
            }

            /*
             * If the company has not reported any months in the current year, return 0 utilization.
             */
            if ($country['reported_months'] === 0) {
                $country['utilization'] = 0;

                return $country;
            }

            /*
             * If the company has reported less than 12 months, we need to calculate the average number of cases
             * per month and add the missing cases to the total number of cases.
             */
            if ($country['reported_months'] < 12) {
                $cases_per_month = round($country['case_count'] / $country['reported_months'], 2);
                $country['case_count'] += $cases_per_month * (12 - $country['reported_months']);
            }

            $utilization = $country['case_count'] / $country['head_count'] * 100;

            $country['utilization'] = round($utilization, 2);

            /*
             * Divide utlization by 100 because Excel interprets values formatted with the percentage format
             * (NumberFormat::FORMAT_PERCENTAGE_00) as fractions of 1
             */
            $country['utilization'] /= 100;

            return $country;
        }));

        // Replace company IDs with company name
        $data = $data->mapWithKeys(fn ($data, $company_id): array => [$companies->where('id', $company_id)->first()->name => $data]);

        Excel::store(new CompanyUtilizationExport($data), Carbon::now()->subMonthNoOverflow()->format('Y-m').'.xlsx', 'private');
        $file_path = storage_path('app/'.Carbon::now()->subMonthNoOverflow()->format('Y-m').'.xlsx');

        Mail::to('anita.tompa@cgpeu.com')->send(new CompanyUtilizationMail($file_path, 'Anita'));
        Mail::to('barbara.kiss@cgpeu.com')->send(new CompanyUtilizationMail($file_path, 'Barbara'));
        Mail::to('peter.janky@cgpeu.com')->send(new CompanyUtilizationMail($file_path, 'Péter'));

        // Remove temp excel file
        if (File::exists($file_path)) {
            File::delete($file_path);
        }
    }
}
