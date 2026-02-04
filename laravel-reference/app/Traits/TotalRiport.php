<?php

namespace App\Traits;

use App\Helpers\CurrencyCached;
use App\Models\CaseInputValue;
use App\Models\Company;
use App\Models\Consultation;
use App\Models\Country;
use App\Models\DirectInvoice;
use App\Models\Invoice;
use App\Models\InvoiceCaseData;
use App\Models\InvoiceData;
use App\Models\Permission;
use App\Models\RiportValue;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait TotalRiport
{
    protected $riport_value_types = [
        RiportValue::TYPE_GENDER,
        RiportValue::TYPE_STATUS,
        RiportValue::TYPE_AGE,
        RiportValue::TYPE_PROBLEM_TYPE,
        RiportValue::TYPE_TYPE_OF_PROBLEM,
        RiportValue::TYPE_CONSULTATION_NUMBER,
    ];

    public function get_riport_data($country = null, $contract_holder = null, $affiliate_id = null): array
    {
        $date_interval = [
            Carbon::now()->now()->subYearNoOverflow()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ];

        $companies = [];
        if ($contract_holder) {
            $companies = Company::query()
                ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', $contract_holder))
                ->pluck('id');
        }

        $usage = [];
        $cases = [];
        $invoices = [];
        $affiliates = [];

        if (! is_null($contract_holder)) {
            $this->case_numbers_by_intervals($companies, 1, $cases, $usage); // Months
            $this->case_numbers_by_intervals($companies, 3, $cases, $usage); // Quarters
            $this->case_numbers_by_intervals($companies, 6, $cases, $usage); // Half Years
        } else {
            $period = CarbonPeriod::create($date_interval[0], '1 month', $date_interval[1]);
            if ($country) {
                $cases = [];

                collect($period)->each(function ($month) use (&$cases, $date_interval, $country, $companies): void {
                    $riport_values = RiportValue::query()
                        ->with('riport')
                        ->where('country_id', $country->id)
                        ->when(! empty($companies), function ($query) use ($companies): void {
                            $query->whereHas('riport', function ($query) use ($companies): void {
                                $query->whereIn('company_id', $companies);
                            });
                        })
                        ->whereHas('riport', function ($query) use ($month): void {
                            $query->where('from', $month);
                        })
                        ->whereIn('type', $this->riport_value_types)
                        ->get();

                    $cases[$month->format('Y-m-d')] = $this->case_numbers($riport_values, $date_interval);
                });
            } else {
                $riport_values = RiportValue::query()
                    ->with('riport')
                    ->when(! empty($companies), function ($query) use ($companies): void {
                        $query->whereHas('riport', function ($query) use ($companies): void {
                            $query->whereIn('company_id', $companies);
                        });
                    })
                    ->whereHas('riport', function ($query) use ($date_interval): void {
                        $query->whereBetween('from', $date_interval);
                    })
                    ->whereIn('type', $this->riport_value_types)
                    ->cursor();

                $cases = $this->case_numbers($riport_values, $date_interval);
            }
        }

        $this->get_invoice_totals(
            $invoices,
            $country,
            $companies,
            Carbon::parse('2023-02-01'), // Begining of sending/generating direct invoices from dashboard
            Carbon::now()->subMonth()->endOfMonth()
        );

        return [
            'usage' => $usage,
            'cases' => $cases,
            'invoices' => $invoices,
            'affiliates' => $affiliates,
        ];
    }

    public function case_numbers_by_intervals($companies, $divide, array &$cases, array &$usage): void
    {
        $date_interval = [
            Carbon::now()->now()->subYearNoOverflow()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ];
        $months = [];
        $period = CarbonPeriod::create(Arr::first($date_interval)->format('Y-m'), '1 month', Arr::last($date_interval)->format('Y-m'));

        foreach ($period as $month) {
            $months[] = $month;
        }

        $intervals = ($divide == 1) ? $months : array_chunk($months, $divide);
        $interval_name = match ($divide) {
            1 => 'month',
            3 => 'quarter',
            6 => 'half_year',
            default => 'month'
        };

        collect($intervals)->each(function ($interval) use ($companies, $interval_name, &$cases, &$usage, &$intervale_date): void {
            $intervale_date = null;
            $riport_values_filter = RiportValue::query()
                ->with('riport')
                ->when(! empty($companies), function ($query) use ($companies): void {
                    $query->whereHas('riport', function ($query) use ($companies): void {
                        $query->whereIn('company_id', $companies);
                    });
                })
                ->when(is_array($interval), function ($query) use ($interval, &$intervale_date): void {
                    $intervale_date = Arr::first($interval)->startOfMonth()->format('Y-m-d').'-'.Arr::last($interval)->endOfMonth()->format('Y-m-d');
                    $query->whereHas('riport', function ($query) use ($interval): void {
                        $query->whereBetween('from', [Arr::first($interval)->endOfMonth()->format('Y-m-d'), Arr::last($interval)->endOfMonth()->format('Y-m-d')]);
                    });
                })
                ->when(! is_array($interval), function ($query) use ($interval, &$intervale_date): void {
                    $month = $interval->format('Y-m');
                    $intervale_date = $month;
                    $query->whereHas('riport', function ($query) use ($month): void {
                        $query->where('from', Carbon::parse($month)->format('Y-m-d'));
                    });
                })
                ->whereIn('type', $this->riport_value_types)
                ->cursor();

            $cases[$interval_name][$intervale_date] = $this->case_numbers($riport_values_filter, $interval);

            $case_numbers = $riport_values_filter->filter(fn ($item): bool => in_array($item->value, ['confirmed', 'client_unreachable', 'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed']))->count();

            $usage[$interval_name][$intervale_date] = round((int) $case_numbers * 100, 1);
        });
    }

    public function case_status($riport_values, array $case_status): array
    {
        $riport_values = $riport_values->filter(fn ($item): bool => $item->type == RiportValue::TYPE_STATUS);
        $riport_values->each(function ($value) use (&$case_status): void {
            $result = match ($value->value) {
                'confirmed' => 'closed',
                'client_unreachable' => 'client_unreachable',
                'client_unreachable_confirmed' => 'client_unreachable',
                'interrupted' => 'interrupted',
                'interrupted_confirmed' => 'interrupted',
                default => 'ongoing'
            };
            $case_status[$result] += 1;
        });

        return $case_status;
    }

    public function case_consultation_types($riport_values, array $case_consultation_types): array
    {
        $riport_values = $riport_values->filter(fn ($item): bool => $item->type == RiportValue::TYPE_PROBLEM_TYPE);
        $riport_values->each(function ($value) use (&$case_consultation_types): void {
            $result = match ($value->value) {
                '1' => 'psychological',
                '2' => 'legal',
                '3' => 'financial',
                '4' => 'other',
                '5' => 'labor_law',
                '6' => 'wellness',
                '7' => 'health_coaching',
                default => 'psychological'
            };
            $case_consultation_types[$result] += 1;
        });

        return $case_consultation_types;
    }

    public function case_numbers($riport_values, $interval): array
    {
        $cases = [];

        if ($interval) {
            $cases['statuses']['ongoing'] = $riport_values
                ->where('type', RiportValue::TYPE_STATUS)
                ->where('is_ongoing', true)
                ->filter(function ($item) use ($interval): bool {
                    $interval_end_from = (is_array($interval)) ? Arr::last($interval)->startOfMonth()->format('Y-m-d') : $interval->format('Y-m-d');

                    return $item->riport->from->format('Y-m-d') == $interval_end_from;
                })
                ->count();
        }

        $cases['statuses']['closed'] = $riport_values
            ->where('type', RiportValue::TYPE_STATUS)
            ->where('value', 'confirmed')
            ->count();

        $cases['statuses']['interrupted'] = $riport_values
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['interrupted', 'interrupted_confirmed'])
            ->count();

        $cases['statuses']['client_unreachable'] = $riport_values
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['client_unreachable', 'client_unreachable_confirmed'])
            ->count();

        // Case age groups
        $cases['ages'] = [];
        CaseInputValue::query()->where('case_input_id', RiportValue::TYPE_AGE)->get()->each(function ($input) use (&$cases): void {
            $cases['ages'][$input->id] = 0;
        });

        $age_types = $riport_values->where('type', RiportValue::TYPE_AGE)->where('is_ongoing', false);
        $age_types->sortBy('value')->groupBy('value')->each(function ($values, $case_input_value_id) use (&$cases): void {
            $cases['ages'][$case_input_value_id] = $values->count();
        });

        // Case problem types
        $cases['problem_type'] = [];
        Permission::query()->get()->each(function ($permission): void {
            $cases['problem_type'][$permission->id] = 0;
        });

        $problem_type_values = $riport_values->where('type', RiportValue::TYPE_PROBLEM_TYPE)->where('is_ongoing', false);
        $problem_type_values->groupBy('value')->each(function ($values, $permission_id) use (&$cases): void {
            $permission = Permission::query()->where('id', $permission_id)->first();
            if ($permission) {
                $cases['problem_type'][$permission->id] = $values->count();
            }
        });

        // Case consultation types
        $cases['consultation_type'] = [];
        CaseInputValue::query()->where('case_input_id', RiportValue::TYPE_TYPE_OF_PROBLEM)->get()->each(function ($input) use (&$cases): void {
            $cases['consultation_type'][$input->id] = 0;
        });
        $consultation_types = $riport_values->where('type', RiportValue::TYPE_TYPE_OF_PROBLEM)->whereIn('value', [80, 81, 82, 83])->where('is_ongoing', false);
        $consultation_types->sortBy('value')->groupBy('value')->each(function ($values, $case_input_value_id) use (&$cases): void {
            $cases['consultation_type'][$case_input_value_id] = $values->count();
        });

        return $cases;
    }

    public function get_invoice_totals(array &$invoices, $country, $companies, $invoice_from, $invoice_to): void
    {
        $currencies = [
            'eur' => [],
            'czk' => [],
            'pln' => [],
            'huf' => [],
            'ron' => [],
            'chf' => [],
            'usd' => [],
            'unknown' => [],
        ];

        $invoices['incomming'] = $currencies;
        $invoices['direct'] = $currencies;

        if ($country) {

            // Get incomming invoces per country
            $invoice_list = Invoice::query()
                ->where('destination_country', $country->id)
                ->whereBetween('payment_deadline', [$invoice_from, $invoice_to])
                ->get();

            $invoice_list->each(function ($item) use (&$invoices): void {
                $invoices['incomming'][($item->currency != '') ? $item->currency : 'unknown'][] = Str::replace(' ', '', $item->grand_total);
            });

            // SUM the country's incomming invoice values
            $invoices['incomming'] = collect($invoices['incomming'])->map(fn ($values): int => array_sum($values))->toArray();

            // Get direct invoces per country
            $country_companies = DB::table('company_x_country')
                ->where('country_id', $country->id)
                ->pluck('company_id')->toArray();

            $invoice_list = DirectInvoice::query()
                ->whereIn('company_id', $country_companies)
                ->whereBetween('created_at', [$invoice_from, $invoice_to])
                ->cursor();

            $invoice_list->each(function ($item) use (&$invoices): void {
                $invoices['direct'][$item->data['billing_data']['currency']][] = get_invoice_net_total($item->data);
            });

            // SUM the country's direct invoice values
            $invoices['direct'] = collect($invoices['direct'])->map(fn ($values): int => array_sum($values))->toArray();
        }

        if ($companies) {
            // Get direct invoices per company
            $invoice_list = DirectInvoice::query()
                ->whereIn('company_id', $companies)
                ->whereBetween('created_at', [$invoice_from, $invoice_to])
                ->get();

            $invoice_list->each(function ($item) use (&$invoices): void {
                $invoices['direct'][$item->data['billing_data']['currency']][] = get_invoice_net_total($item->data);
            });

            // SUM the company's direct invoice values
            $invoices['direct'] = collect($invoices['direct'])->map(fn ($values): int => array_sum($values))->toArray();
        }
    }

    public function get_affiliate_invoice_numbers($id, $year = null, $month = null): array
    {
        $interval = [];
        $converter = new CurrencyCached(60 * 60 * 24);

        if ($year) {
            if ($year == (int) Carbon::now()->format('Y')) {
                $interval = [Carbon::now()->startOfYear()->addMonth(), Carbon::now()->subQuarter()->endOfQuarter()->addMonth()];

                if ($month) {
                    $interval = [Carbon::now()->setMonth($month)->addMonth()->startOfMonth(), Carbon::now()->setMonth($month)->addMonth()->endOfMonth()];
                }
            } else {
                $interval = [Carbon::now()->setYear($year)->startOfYear(), Carbon::now()->setYear($year)->addYear()->startOfYear()->addDays(9)->format('Y-m-d')];
                if ($month) {
                    $interval = [Carbon::now()->setYear($year)->setMonth($month)->addMonth()->startOfMonth(), Carbon::now()->setYear($year)->setMonth($month)->addMonth()->endOfMonth()];
                }
            }
        } else {
            $interval = [Carbon::now()->startOfYear(), Carbon::now()->subQuarter()->endOfQuarter()];
            if ($month) {
                $interval = [Carbon::now()->setMonth($month)->startOfMonth(), Carbon::now()->setMonth($month)->endOfMonth()];
            }
        }
        $invoices = Invoice::query()
            ->where('user_id', $id)
            ->whereNotNull('currency')
            ->whereBetween('date_of_issue', $interval)
            ->get();

        $invoice_totals = 0;
        $consultation_total = 0;
        $invoice_data = InvoiceData::query()
            ->where('user_id', $id)
            ->whereNotNull('currency')
            ->first();
        $hourly_rate = ($invoice_data->currency) ? $converter->convert(
            (float) Str::replace(' ', '', $invoice_data->hourly_rate_50 ?: $invoice_data->hourly_rate_30),
            'EUR',
            strtoupper((string) $invoice_data->currency)
        ) : 0;

        $invoices->each(function ($invoice) use (&$invoice_totals, &$consultation_total, $converter): void {
            if ($invoice->currency) {
                $raw_filtered_value = filter_var($invoice->getRawOriginal('grand_total'), FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
                $invoice_totals += $converter->convert((float) Str::replace(' ', '', $raw_filtered_value), 'EUR', strtoupper((string) $invoice->currency));
                $consultation_total += $invoice->case_datas->sum('consultations_count');
            }
        });

        return [
            'invoice_totals' => $invoice_totals,
            'consultation_total' => $consultation_total,
            'hourly_rate' => $hourly_rate,
            'country' => $invoice_data->country->name,
        ];
    }

    public function get_affiliate_consultation_numbers($id, $year = null)
    {
        $interval = [];

        if ($year) {
            if ($year == (int) Carbon::now()->format('Y')) {
                if (Carbon::now()->quarter == 1) { // In the first quarter of the year there is no current data available
                    $interval = [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()];
                } else {
                    $interval = [Carbon::now()->startOfYear(), Carbon::now()->subQuarter()->endOfQuarter()];
                }
            } else {
                $interval = [Carbon::now()->setYear($year)->startOfYear(), Carbon::now()->setYear($year)->endOfYear()];
            }
        } else {
            $interval = [Carbon::now()->startOfYear(), Carbon::now()->subQuarter()->endOfQuarter()];
        }

        return InvoiceCaseData::query()
            ->whereHas('expert', function ($query) use ($id): void {
                $query->where('id', $id);
            })
            ->whereBetween('created_at', $interval)
            ->count();
    }

    public function get_country_expert_invoice_total($country_id, $month = null): array
    {
        $country_invoices = [
            'eur' => 0,
            'czk' => 0,
            'pln' => 0,
            'huf' => 0,
            'ron' => 0,
            'chf' => 0,
            'usd' => 0,
            'unknown' => 0,
        ];

        $experts = User::query()
            ->with('invoices')
            ->has('invoices')
            ->whereHas('expert_data', function ($query): void {
                $query->where('is_cgp_employee', 0);
            })
            ->where('country_id', $country_id)
            ->get();

        $date_interval = [
            'from' => ($month) ? Carbon::parse($month)->startOfmonth() : Carbon::now()->now()->subYearNoOverflow()->startOfMonth(),
            'to' => ($month) ? Carbon::parse($month)->endOfmonth() : Carbon::now()->subMonth()->endOfMonth(),
        ];

        $experts->each(function ($expert) use (&$country_invoices, $date_interval): void {
            $invoices = $expert->invoices->filter(fn ($item): bool => Carbon::parse($item->date_of_issue)->between($date_interval['from'], $date_interval['to']));
            $invoices->each(function ($invoice) use (&$country_invoices): void {
                $country_invoices[$invoice->currency ?: 'unknown'] += Str::replace(' ', '', $invoice->grand_total);
            });
        });

        return $country_invoices;
    }

    public function get_querters($months): array
    {
        $quarters = [];
        $quarter_iteration = 1;
        collect(array_chunk($months->toArray(), 3))->each(function ($quarter) use (&$quarters, &$quarter_iteration): void {
            $quarters['q'.$quarter_iteration] = $quarter;
            $quarter_iteration++;
        });

        return $quarters;
    }
}
