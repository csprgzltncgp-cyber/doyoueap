<?php

use App\Enums\CaseExpertStatus;
use App\Enums\ContractHolderCompany;
use App\Enums\InvoicingType;
use App\Helpers\CurrencyCached;
use App\Helpers\QuarterDates;
use App\Models\ActivityPlan;
use App\Models\AssetOwner;
use App\Models\Cases;
use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\DirectBillingData;
use App\Models\DirectInvoice;
use App\Models\EapOnline\EapRiport;
use App\Models\Envelope;
use App\Models\InvoiceItem;
use App\Models\OrgData;
use App\Models\Riport;
use App\Models\RiportValue;
use App\Models\User;
use App\Scopes\CountryScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

if (! function_exists('calculate_percentage')) {
    function calculate_percentage($value, $total): int|float
    {
        if ($total == 0) {
            return 0;
        }

        return round($value / $total * 100);
    }
}

if (! function_exists('elapsed_time')) {
    function elapsed_time(?string $start, ?string $end): ?string
    {
        if ($start === null || $start === '' || ($end === null || $end === '')) {
            return null;
        }

        $parsed_start = Carbon::parse(now()->year.'-'.now()->month.'-'.now()->day.' '.$start);
        $parsed_end = Carbon::parse(now()->year.'-'.now()->month.'-'.now()->day.' '.$end);

        return $parsed_end->diff($parsed_start)->format('%h '.__('workshop.hour').' %I '.__('workshop.minute'));
    }
}

if (! function_exists('get_last_quarter')) {
    function get_last_quarter()
    {
        if (config('app.env') != 'production') {
            return Carbon::now()->quarter;
        }

        return Carbon::now()->startOfQuarter()->subDay()->quarter;
    }
}

if (! function_exists('has_riport_in_quarter')) {
    function has_riport_in_quarter($quarter): bool
    {
        // check if today is in the first quarter of the current year
        if (Carbon::now()->quarter == 1) {
            $from = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3);
        } else {
            $from = Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->startOfYear()->addMonths($quarter * 3);
        }

        $riports = Riport::query()
            ->where([
                'company_id' => Auth::user()->companies()->first()->id,
                'is_active' => true,
            ])
            ->whereDate('from', '>=', $from->format('Y-m-d'))
            ->whereDate('to', '<=', $to->subDay()->format('Y-m-d'))
            ->get();

        if ($riports->isEmpty()) {
            return false;
        }

        return $riports->count() > 0;
    }
}

if (! function_exists('has_eap_riport_in_quarter')) {
    function has_eap_riport_in_quarter($quarter): bool
    {
        // check if today is in the first quarter of the current year
        if (Carbon::now()->quarter == 1) {
            $from = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3);
        } else {
            $from = Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3);
            $to = Carbon::now()->startOfYear()->addMonths($quarter * 3);
        }

        return EapRiport::query()->where([
            'company_id' => auth()->user()->companies()->first()->id,
            'is_active' => true,
            'from' => $from->format('Y-m-d'),
            'to' => $to->subDay()->format('Y-m-d'),
        ])->exists();
    }
}

if (! function_exists('get_country_code_from_client_language')) {
    function get_country_code_from_client_language($language_input_id): string
    {
        return match ((int) $language_input_id) {
            15 => 'hu',
            5 => 'pl',
            1 => 'cz',
            2 => 'sk',
            3 => 'ro',
            8 => 'bg',
            13 => 'ua',
            23 => 'es',
            default => 'en',
        };
    }
}

if (! function_exists('calculate_program_usage')) {
    function calculate_program_usage($company, $country, $org_data, $month = null, $year = null): int|float
    {
        $year ??= Carbon::now()->subYearNoOverflow()->year;

        if ($company->id === 865 && $country->id === 1 && $year === 2023) {
            return 3;
        }

        $cases_number = RiportValue::query()
            ->where('country_id', $country->id)
            ->whereHas('riport', function ($query) use ($company): void {
                $query->where('company_id', $company->id);
            })
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['confirmed', 'client_unreachable', 'client_unreachable_confirmed', 'interrupted', 'interrupted_confirmed'])
            ->when(! empty($month), function ($query) use ($month): void {
                $query->whereMonth('created_at', $month);
            })
            ->where('created_at', '>', Carbon::createFromDate($year)->startOfYear()->addMonth())
            ->where('created_at', '<', Carbon::createFromDate($year)->endOfYear()->addMonthNoOverflow())
            ->count();

        $in_porgress_cases_number = get_in_progress_cases_count(company_id: $company->id, country_id: $country->id, year: $year, quarter: 4);

        if (empty($month)) {
            /*
             * Get the number of reported months in the current year. Now we are checking the riport 'from'
             * and 'to' fields, that's why we do not need to add months to the dates.
             */
            $reported_months = Riport::query()
                ->where('company_id', $company->id)
                ->where('is_active', true)
                ->where('from', '>=', Carbon::createFromDate($year)->startOfYear())
                ->where('to', '<=', Carbon::createFromDate($year)->endOfYear())
                ->count();

            /*
             * If the company has not reported any months in the current year, we return 0.
             */
            if ($reported_months === 0) {
                return 0;
            }

            /*
             * If the company has reported less than 12 months, we need to calculate the average number of cases
             * per month and add the missing cases to the total number of cases.
             */
            if ($reported_months < 12) {
                // Calculate the average number of cases per month and always round up to the nearest integer.
                $cases_per_month = ceil($cases_number / $reported_months);
                $cases_number += $cases_per_month * (12 - $reported_months);
            }
        }

        $workshop_count = RiportValue::query()
            ->where('country_id', $country->id)
            ->whereHas('riport', function ($query) use ($company): void {
                $query->where('company_id', $company->id);
            })
            ->where('type', RiportValue::TYPE_WORKSHOP_NUMBER_OF_PARTICIPANTS)
            ->when(! empty($month), function ($query) use ($month): void {
                $query->whereMonth('created_at', $month);
            })
            ->where('created_at', '>', Carbon::createFromDate($year)->startOfYear()->addMonth())
            ->where('created_at', '<', Carbon::createFromDate($year)->endOfYear()->addMonthNoOverflow())
            ->sum('value');

        $health_day_count = RiportValue::query()
            ->where('country_id', $country->id)
            ->whereHas('riport', function ($query) use ($company): void {
                $query->where('company_id', $company->id);
            })
            ->where('type', RiportValue::TYPE_HEALTH_DAY_NUMBER_OF_PARTICIPANTS)
            ->when(! empty($month), function ($query) use ($month): void {
                $query->whereMonth('created_at', $month);
            })
            ->where('created_at', '>', Carbon::createFromDate($year)->startOfYear()->addMonth())
            ->where('created_at', '<', Carbon::createFromDate($year)->endOfYear()->addMonthNoOverflow())
            ->sum('value');

        $expert_outplacement_count = RiportValue::query()
            ->where('country_id', $country->id)
            ->whereHas('riport', function ($query) use ($company): void {
                $query->where('company_id', $company->id);
            })
            ->where('type', RiportValue::TYPE_EXPERT_OUTPLACEMENT_NUMBER_OF_PARTICIPANTS)
            ->when(! empty($month), function ($query) use ($month): void {
                $query->whereMonth('created_at', $month);
            })
            ->where('created_at', '>', Carbon::createFromDate($year)->startOfYear()->addMonth())
            ->where('created_at', '<', Carbon::createFromDate($year)->endOfYear()->addMonthNoOverflow())
            ->sum('value');

        $crisis_count = RiportValue::query()
            ->where('country_id', $country->id)
            ->whereHas('riport', function ($query) use ($company): void {
                $query->where('company_id', $company->id);
            })
            ->where('type', RiportValue::TYPE_CRISIS_NUMBER_OF_PARTICIPANTS)
            ->when(! empty($month), function ($query) use ($month): void {
                $query->whereMonth('created_at', $month);
            })
            ->where('created_at', '>', Carbon::createFromDate($year)->startOfYear()->addMonth())
            ->where('created_at', '<', Carbon::createFromDate($year)->endOfYear()->addMonthNoOverflow())
            ->sum('value');

        if ((int) $org_data->head_count == 0) {
            return 0;
        }

        return round((((int) $cases_number + $in_porgress_cases_number + (int) $workshop_count + (int) $health_day_count + (int) $expert_outplacement_count + (int) $crisis_count) / (int) $org_data->head_count) * 100, 1);
    }
}

if (! function_exists('get_phq9_language')) {
    function get_phq9_language($case): string
    {
        /*
         *   Case Input values:
         *   150 -> ro
         *   151 -> sr
         *   148 -> cz
         */

        $case_language_id = (int) $case->values->where('case_input_id', 32)->first()->value;
        if ($case_language_id == 150) {
            return 'ro';
        }
        if ($case_language_id == 151) {
            return 'sr';
        }

        if ($case_language_id == 148) {
            return 'cz';
        }

        return 'en';
    }
}

if (! function_exists('all_completion_certificates_printed_in_month')) {
    function all_completion_certificates_printed_in_month($month): bool
    {
        $completion_certificates = CompletionCertificate::query()
            ->whereHas('direct_invoice', fn ($query) => $query
                ->whereDate('to', Carbon::parse($month)->endOfMonth()->format('Y-m-d'))
                ->has('completion_certificate'))->get();

        foreach ($completion_certificates as $completion_certificate) {
            if (empty($completion_certificate->printed_at)) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('all_completion_certificates_sent_in_month')) {
    function all_completion_certificates_sent_in_month($month): bool
    {
        $completion_certificates = CompletionCertificate::query()
            ->whereHas('direct_invoice', fn ($query) => $query
                ->whereDate('to', Carbon::parse($month)->endOfMonth()->format('Y-m-d'))
                ->has('completion_certificate'))->get();

        foreach ($completion_certificates as $completion_certificate) {
            if (empty($completion_certificate->sent_at)) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('all_enevelopes_printed_in_month')) {
    function all_enevelopes_printed_in_month($month): bool
    {
        $enevelopes = Envelope::query()
            ->whereHas('direct_invoice', fn ($query) => $query
                ->whereDate('to', Carbon::parse($month)->endOfMonth()->format('Y-m-d'))
                ->has('envelope'))->get();

        foreach ($enevelopes as $enevelope) {
            if (empty($enevelope->printed_at)) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('has_missing_information_on_direct_invoice')) {
    function has_missing_information_on_direct_invoice(DirectInvoice $direct_invoice): bool
    {
        $data = $direct_invoice->data;

        // po number
        if ($data['invoice_data']['is_po_number_changing'] && empty($data['invoice_data']['po_number'])) {
            return true;
        }

        // volume and amount
        foreach ($data['invoice_items'] as $invoice_item) {
            if (! in_array($invoice_item['input'], [InvoiceItem::INPUT_TYPE_AMOUNT, InvoiceItem::INPUT_TYPE_MULTIPLICATION])) {
                continue;
            }

            // amount input
            if ((int) $invoice_item['input'] === InvoiceItem::INPUT_TYPE_AMOUNT && empty($invoice_item['amount']['value'])) {
                return true;
            }
            // multiplication input
            if ((int) $invoice_item['input'] !== InvoiceItem::INPUT_TYPE_MULTIPLICATION) {
                continue;
            }
            if (empty($invoice_item['volume']['value'])) {
                return true;
            }
            if (empty($invoice_item['amount']['value'])) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('has_missing_information_on_direct_invoices')) {
    function has_missing_information_on_direct_invoices($direct_invoices): bool
    {
        foreach ($direct_invoices as $direct_invoice) {
            if (has_missing_information_on_direct_invoice($direct_invoice)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('has_invoice_with_no_missing_company_and_direct_invoice_information')) {
    function has_invoice_with_no_missing_company_and_direct_invoice_information($direct_invoices): bool
    {
        foreach ($direct_invoices as $direct_invoice) {
            if (! $direct_invoice->company) {
                continue;
            }

            if (! has_missing_information_on_direct_invoice($direct_invoice) && ! has_company_missing_information($direct_invoice->company)) {
                if (is_invoice_done($direct_invoice)) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }
}

if (! function_exists('has_invoice_with_done_status')) {
    function has_invoice_with_done_status($direct_invoices): bool
    {
        foreach ($direct_invoices as $direct_invoice) {
            if (is_invoice_done($direct_invoice)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('is_invoices_generatable')) {
    function is_invoices_generatable($direct_invoices): bool
    {
        foreach ($direct_invoices as $direct_invoice) {
            if (! has_missing_information_on_direct_invoice($direct_invoice)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('is_invoice_done')) {
    function is_invoice_done(DirectInvoice $direct_invoice): bool
    {
        $data = $direct_invoice->data;

        $direct_invoice->load(['completion_certificate', 'envelope']);

        // check if invoice is generated
        if (empty($direct_invoice->invoice_number)) {
            return false;
        }

        // check if donwloaded at if needed
        if ($data['billing_data']['send_invoice_by_post'] && empty($direct_invoice->downloaded_at)) {
            return false;
        }

        // check if sent at if needed
        if ($data['billing_data']['send_invoice_by_email'] && empty($direct_invoice->sent_at)) {
            return false;
        }

        // check if uploaded at if needed
        if ($data['billing_data']['upload_invoice_online'] && empty($direct_invoice->invoice_uploaded_at)) {
            return false;
        }

        // check if completion certificate is  done
        if (! empty($direct_invoice->completion_certificate)) {
            // if ($data['billing_data']['send_completion_certificate_by_post']) {
            //     if (empty($direct_invoice->completion_certificate->printed_at)) {
            //         return false;
            //     }
            // }

            if ($data['billing_data']['send_completion_certificate_by_email'] && empty($direct_invoice->completion_certificate->sent_at)) {
                return false;
            }

            if ($data['billing_data']['upload_completion_certificate_online'] && empty($direct_invoice->completion_certificate->uploaded_at)) {
                return false;
            }
        }

        // // check if envelope is done
        // if (!empty($direct_invoice->envelope)) {
        //     if ($data['billing_data']['send_invoice_by_post']) {
        //         if (empty($direct_invoice->envelope->printed_at)) {
        //             return false;
        //         }
        //     }
        // }

        return true;
    }
}

if (! function_exists('is_invoices_done')) {
    function is_invoices_done($directInvoices): bool
    {
        if ($directInvoices->count() <= 0) {
            return false;
        }

        foreach ($directInvoices as $directInvoice) {
            if (! is_invoice_done($directInvoice)) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('validate_direct_invoice_data')) {
    function validate_direct_invoice_data($data, $inside_eu = false): array
    {
        $errors = [];

        if (empty($data->name)) {
            $errors[] = 'directInvoiceData.name';
        }

        if (empty($data->country)) {
            $errors[] = 'directInvoiceData.country';
        }

        if (empty($data->city)) {
            $errors[] = 'directInvoiceData.city';
        }

        if (empty($data->street)) {
            $errors[] = 'directInvoiceData.street';
        }

        if (empty($data->postal_code)) {
            $errors[] = 'directInvoiceData.postal_code';
        }

        if (empty($data->house_number)) {
            $errors[] = 'directInvoiceData.house_number';
        }

        if (empty($data->tax_number) && empty($data->community_tax_number) && empty($data->group_id)) {
            $errors[] = 'directInvoiceData.tax_number';
            $errors[] = 'directInvoiceData.community_tax_number';
            $errors[] = 'directInvoiceData.group_id';
        }

        if (! empty($data->tax_number) && ! empty($data->community_tax_number)) {
            $errors[] = 'directInvoiceData.tax_number';
            $errors[] = 'directInvoiceData.community_tax_number';
        }

        if (! empty($data->group_id) && ! empty($data->community_tax_number)) {
            $errors[] = 'directInvoiceData.group_id';
            $errors[] = 'directInvoiceData.community_tax_number';
        }

        // tax number must only contain letters, numbers, dashes and underscores.
        if (! empty($data->community_tax_number) && ! preg_match('/^[a-zA-Z0-9_-]+$/', (string) $data->community_tax_number)) {
            $errors[] = 'directInvoiceData.community_tax_number';
        }

        // tax number for EU countries should contain letters for the first two characters
        if ($inside_eu && ! preg_match('/^[A-Za-z]{2}/', $data->community_tax_number)) {
            $errors[] = 'directInvoiceData.community_tax_number';
        }

        if (! empty($data->group_id) && ! valid_hu_tax_number($data->group_id)) {
            $errors[] = 'directInvoiceData.group_id';
        }

        if (! empty($data->tax_number) && ! valid_hu_tax_number($data->tax_number)) {
            // $errors[] = 'directInvoiceData.tax_number';
        }

        if (empty($data->payment_deadline) || ! is_numeric($data->payment_deadline) || (int) $data->payment_deadline < 0 || (int) $data->payment_deadline > 365) {
            $errors[] = 'directInvoiceData.payment_deadline';
        }

        return $errors;
    }
}

if (! function_exists('validate_direct_billing_data')) {
    function validate_direct_billing_data($data): array
    {
        $errors = [];

        if (empty($data->invoice_language)) {
            $errors[] = 'directBillingData.invoice_language';
        }

        if (! $data->inside_eu && ! $data->outside_eu && (empty($data->vat_rate) || ! is_numeric($data->vat_rate) || (int) $data->vat_rate < 0)) {
            $errors[] = 'directBillingData.vat_rate';
        }

        if (empty($data->billing_frequency)) {
            $errors[] = 'directBillingData.billing_frequency';
        }

        if (empty($data->currency)) {
            $errors[] = 'directBillingData.currency';
        }

        if (
            empty($data->send_invoice_by_post) &&
            empty($data->send_invoice_by_email) &&
            empty($data->upload_invoice_online)
        ) {
            $errors[] = 'directBillingData.send_invoice_by_post';
            $errors[] = 'directBillingData.send_invoice_by_email';
            $errors[] = 'directBillingData.upload_invoice_online';
        }

        return $errors;
    }
}

if (! function_exists('validate_invoice_item')) {
    /**
     * @return mixed[]
     */
    function validate_invoice_item($data): array
    {
        $errors = [];

        if ((int) $data->input !== InvoiceItem::INPUT_TYPE_MULTIPLICATION) {
            return $errors;
        }

        if (empty($data->volume->name)) {
            $errors[] = 'invoiceItem.volume';
        }

        if (empty($data->amount->name)) {
            $errors[] = 'invoiceItem.amount';
        }

        if (! optional($data->amount)->is_changing && empty($data->amount->value)) {
            $errors[] = 'invoiceItem.amount_value';
        }

        if (! optional($data->volume)->is_changing && empty($data->volume->value)) {
            $errors[] = 'invoiceItem.volume_value';
        }

        return $errors;
    }
}

if (! function_exists('has_company_missing_information')) {
    function has_company_missing_information(Company $company, $country_id = null): bool
    {
        $key = 'missing-company-information-'.$company->id;

        if ($country_id) {
            $key .= '-'.$country_id;
        }

        return Cache::rememberForever($key, function () use ($company, $country_id): bool {
            $company->loadMissing(['direct_invoice_datas', 'direct_billing_datas', 'invoice_items', 'invoice_items.volume', 'invoice_items.amount']);

            $direct_invoice_datas = empty($country_id) ? $company->direct_invoice_datas : $company->direct_invoice_datas->where('country_id', $country_id);

            if ($direct_invoice_datas->count() <= 0) {
                return true;
            }

            if (! empty($country_id)) {
                $direct_billing_datas = DirectBillingData::query()
                    ->where('country_id', $country_id)
                    ->where('company_id', $company->id)
                    ->whereIn('direct_invoice_data_id', $direct_invoice_datas->pluck('id'))
                    ->get();

                $invoice_items = InvoiceItem::query()
                    ->where('country_id', $country_id)
                    ->where('company_id', $company->id)
                    ->whereIn('direct_invoice_data_id', $direct_invoice_datas->pluck('id'))
                    ->get();
            } else {
                $direct_billing_datas = $company->direct_billing_datas;
                $invoice_items = $company->invoice_items;
            }

            foreach ($direct_invoice_datas as $direct_invoice_data) {
                if (validate_direct_invoice_data($direct_invoice_data) !== []) {
                    return true;
                }
            }

            foreach ($direct_billing_datas as $direct_billing_data) {
                if (validate_direct_billing_data($direct_billing_data) !== []) {
                    return true;
                }
            }

            foreach ($invoice_items as $invoice_item) {
                if (validate_invoice_item($invoice_item) !== []) {
                    return true;
                }
            }

            return false;
        });
    }
}

if (! function_exists('has_companies_missing_information')) {
    function has_companies_missing_information($companies): bool
    {
        foreach ($companies as $company) {
            if (has_company_missing_information($company)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('get_direct_invoices_in_month')) {
    function get_direct_invoices_in_month($month, $contract_holder = null)
    {
        if (is_null($contract_holder)) {
            return DirectInvoice::query()
                ->with('company')
                ->where('to', Carbon::parse($month)->endOfMonth()->format('Y-m-d'))
                ->whereNotIn('company_id', array_column(ContractHolderCompany::cases(), 'value'))
                ->get();
        }

        return DirectInvoice::query()
            ->with('company')
            ->whereDate('to', Carbon::parse($month)->endOfMonth()->format('Y-m-d'))
            ->where('company_id', $contract_holder)
            ->get();
    }
}

if (! function_exists('get_companies_in_month')) {
    /**
     * @return mixed[]
     */
    function get_companies_in_month($month, $contract_holder = null): array
    {
        $direct_invoices = get_direct_invoices_in_month($month, $contract_holder);

        $companies = [];

        foreach ($direct_invoices as $direct_invoice) {
            if ($direct_invoice->company) {
                $companies[] = $direct_invoice->company;
            }

        }

        return array_unique($companies);
    }
}

if (! function_exists('valid_hu_tax_number')) {
    function valid_hu_tax_number($tax_number): bool
    {
        $regex = '/^\d{8}\-[1-5]\-(?:0[2-9]|[13]\d|2[02-9]|4[0-4]|51)$/';

        return (bool) preg_match($regex, (string) $tax_number);
    }
}

if (! function_exists('has_missing_information_on_asset')) {
    function has_missing_information_on_asset(AssetOwner $owner): bool
    {
        $assets = $owner->assets;

        foreach ($assets as $items) {
            if ($items->name == '---' || $items->name == '-') {
                return true;
            }
            if ($items->own_id == '---' || $items->own_id == '-') {
                return true;
            }
            if (empty($items->date_of_purchase)) {
                return true;
            }

            // search mising information in phones
            if ($items->asset_type_id == 3) {
                if ($items->phone_num == '---' || $items->phone_num == '-') {
                    return true;
                }
                if ($items->pin == '---' || $items->pin == '-') {
                    return true;
                }
                if ($items->provider == '---' || $items->provider == '-') {
                    return true;
                }
                if ($items->package == '---' || $items->package == '-') {
                    return true;
                }
            }

            // search mising information in sim cards
            if ($items->asset_type_id == 14) {
                if ($items->phone_num == '---' || $items->phone_num == '-') {
                    return true;
                }
                if ($items->pin == '---' || $items->pin == '-') {
                    return true;
                }
                if ($items->puk == '---' || $items->puk == '-') {
                    return true;
                }
                if ($items->provider == '---' || $items->provider == '-') {
                    return true;
                }
                if ($items->package == '---' || $items->package == '-') {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('query_available_experts')) {
    function query_available_experts(
        ?int $is_crisis = null,
        ?int $permission_id = null,
        ?int $country_id = null,
        ?int $city_id = null,
        ?int $specialization_id = null,
        ?int $language_skill_id = null,
        ?int $consultation_minute = null,
        ?bool $is_personal = null,
        ?Cases $case = null,
        $skip_ids = null,
        ?int $company_id = null,
        ?int $problem_details = null,
        ?bool $ignore_language_skill = false,
        ?bool $ignore_case_limit = false
    ) {

        /**
         * If the:
         *
         * - permission_id is Single session therapy(17)
         *
         * then return experts who have single session invoice pricing
         */
        if ($permission_id === 17 && ! $is_crisis) {
            return User::query()
                ->whereHas('invoice_datas', fn (Builder $query) => $query->whereNotNull('single_session_rate'))
                ->get();
        }

        /**
         * If the:
         *
         * - company is Nestlé Hungária Kft.(760)
         * - case problem details(case input 16) is Dietetics (68 or 355)
         *
         * then return only one expert, Sziráki Zsófia (1382):
         */
        if ($company_id === 760 && ($problem_details === 68 || $problem_details === 355) && ! $is_crisis) {
            $user = User::query()
                ->where('id', 1382)
                ->where('active', 1)
                ->get();

            if (! $user->isEmpty()) {
                return $user;
            }
        }

        /**
         * If the:
         *
         * - The country is hungary(1) and permission is health coaching (7)
         *
         * IF company IS "Henkel(24)" than return Farkasdi Edina(109) else return Farkasdi Edina(109), Gellén-Kiss Szilvia (1125),  Petri Nóra(131) based on priorities:
         */
        if ($country_id === 1 && $permission_id === 7 && ! $is_crisis) {
            if ($company_id === 24) {// Henkel
                $user = User::query()
                    ->where('id', 109) // Farkasdi Edina
                    ->with(['language_skills', 'expert_data'])
                    ->where('active', 1)
                    ->get();
            } else {
                $expert_case_datas = User::query()
                    ->whereIn('id', [109, 1125, 131])
                    ->with(['language_skills', 'expert_data'])
                    ->where('active', 1)
                    ->get()
                    ->map(fn (User $expert): array => [
                        'expert_id' => $expert->id,
                        'max_inprogress_cases' => $expert->expert_data?->max_inprogress_cases,
                        'cases_in_current_month' => $expert->cases()
                            ->whereBetween('cases.created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                            ->count(),
                    ]);

                $expert_id = null;

                // Check Petri Nóra (131) (contractual obligation to get 10 cases/month should be fulfilled first)
                $expert = collect($expert_case_datas)->where('expert_id', 131)->first();

                if ($expert && $expert['cases_in_current_month'] < $expert['max_inprogress_cases']) {
                    $expert_id = $expert['expert_id'];
                } else {
                    $expert_a = collect($expert_case_datas)->where('expert_id', 109)->first(); // Farkasdi Edina
                    $expert_b = collect($expert_case_datas)->where('expert_id', 1125)->first(); // Gellén-Kiss Szilvia

                    if (! $expert_a && $expert_b) {
                        $expert_id = $expert_b['expert_id'];
                    }

                    if (! $expert_b && $expert_a) {
                        $expert_id = $expert_a['expert_id'];
                    }

                    /**
                     * Check Farkasdi Edina's cases percenatges compared to Gellén-Kiss Szilvia
                     * Case distributin should be 50-50 between the two experts
                     */
                    if ($expert_a && $expert_b) {
                        $case_percenatge = $expert_a['cases_in_current_month'] / ($expert_a['cases_in_current_month'] + $expert_b['cases_in_current_month']) * 100;

                        $expert_id = round($case_percenatge) < 50 ? $expert_a['expert_id'] : $expert_b['expert_id'];
                    }
                }

                $user = User::query()
                    ->where('id', $expert_id)
                    ->with(['language_skills', 'expert_data'])
                    ->where('active', 1)
                    ->get();
            }

            if (! $user->isEmpty()) {
                return $user;
            }
        }

        /**
         * If the:
         *
         * - The country is Czech republic (3) and permission is psychological (1)
         * - contact type is personal(city is Prague(89)) or not personal
         *
         * then return Amanda Mataija (Prague Integration)(1512)
         */
        if ($permission_id === 1 && $country_id === 3 && (! $is_personal || $city_id === 89) && ! $is_crisis) {
            $user = User::query()
                ->where('id', 1512) // Amanda Mataija
                ->with(['language_skills', 'expert_data'])
                ->where('active', 1)
                ->get();

            if (! $user->isEmpty()) {
                return $user;
            }
        }
        /**
         * If the:
         *
         * - The country is hungary(1) and permission is finance (3)
         * - The language skill is Hungarian (15) or English (4)
         *
         * then return CGP Pénzügyi Tanácsadás- Adózás IF the problem detail is Taxation(174), else return Nagy Bálint (1054):
         */
        if ($country_id === 1 && $permission_id === 3 && in_array($language_skill_id, [4, 15]) && ! $is_crisis) {
            if ($problem_details === 174 && $language_skill_id === 15) { // Taxation/Adózás & Hungarian
                $user = User::query()
                    ->where('id', 1778) // CGP Pénzügyi Tanácsadás- Adózás
                    ->with(['language_skills', 'expert_data'])
                    ->where('active', 1)
                    ->get();

                if (! $user->isEmpty()) {
                    return $user;
                }
            }

            if ($problem_details !== 174) { // Not taxation
                $user = User::query()
                    ->where('id', 1054) // Nagy Bálint
                    ->with(['language_skills', 'expert_data'])
                    ->where('active', 1)
                    ->get();

                if (! $user->isEmpty()) {
                    return $user;
                }
            }
        }

        /**
         * If the:
         *
         * - company is Philip Morris Products S.A./ PMI (322)
         * - country is Switzerland (5)
         * - permission is psychological (1)
         * - language skill is English, French, German or Italian
         *
         * then return one of 3 experts based on language skill (Cyril Méan(650), Rebecca Seger(192), Metlem Kusku Schmidt(1142) )
         */
        if ($company_id === 322 && $country_id === 5 && $permission_id === 1 && in_array($language_skill_id, [4, 10, 11, 12]) && ! $is_crisis) {
            $query = User::query()->with(['language_skills', 'expert_data']);

            match ($language_skill_id) {
                4 => $query->where('id', 1142)->where('active', 1), // 3 - en
                12 => $query->where('id', 650)->where('active', 1), // 12 - fr
                10, 11 => $query->where('id', 192)->where('active', 1), // 10 - it, 11 - de
                default => null,
            };

            $user = $query->get();

            if (! $user->isEmpty()) {
                return $user;
            }
        }

        /**
         * If the:
         *
         * - is personal
         * - city is Budapest
         * - is psyhological
         * - country is Hungary
         *
         * then return Szakács V. Viola / Almafa (1503) only
         */
        if (($is_personal && $city_id === 10 || ! $is_personal) && $permission_id === 1 && $country_id === 1 && ! $is_crisis) {
            $user = User::query()
                ->where('id', 1503)
                ->with(['language_skills', 'expert_data'])
                ->where('active', 1)
                ->get();

            if ($user->isNotEmpty()) {
                return $user;
            }
        }

        /**
         * If the:
         *
         * - IF personal THAN IF city is Bratislava(42)
         * - is psyhological
         * - country is Slovakia(4)
         *
         * then return EFT Institute Slovakia s.r.o. (1821) only
         */
        if (($is_personal && $city_id === 42 || ! $is_personal) && $permission_id === 1 && $country_id === 4 && ! $is_crisis) {
            $user = User::query()
                ->where('id', 1821)
                ->with(['language_skills', 'expert_data'])
                ->where('active', 1)
                ->get();

            if ($user->isNotEmpty()) {
                return $user;
            }
        }

        $org_data = OrgData::query()->where([
            'company_id' => $company_id,
            'country_id' => $country_id,
        ])->first(['contract_holder_id']);

        $experts = User::query()
            ->with(['language_skills', 'specializations', 'expert_data', 'cities', 'invoice_datas'])
            ->where('type', 'expert')
            ->where('active', 1)
            ->where('locked', 0)
            ->whereNotIn('id', [1345, 1344, 1343, 1342, 1341])
            ->whereNotNull('last_login_at')
            ->when((! $is_crisis && $is_personal), function ($query) use ($city_id): void {
                $query->whereHas('cities', function ($query2) use ($city_id): void {
                    $query2->where('city_id', $city_id);
                });
            })
            ->whereHas('permission', function ($query) use ($permission_id): void {
                $query->where('permission_id', $permission_id);
            })
            ->when($is_crisis, function ($query) use ($country_id): void {
                $query->whereHas('expertCrisisCountries', function ($query2) use ($country_id): void {
                    $query2->where('country_id', $country_id);
                });
            })
            ->when(! $is_crisis, function ($query) use ($country_id): void {
                $query->whereHas('expertCountries', function ($query2) use ($country_id): void {
                    $query2->where('country_id', $country_id);
                });
            })
            ->when(! $is_crisis && $permission_id === 1 && ! $ignore_case_limit, function ($query): void {
                $query->whereHas('expert_data', function ($query): void {
                    $query->where('can_accept_more_cases', 1);
                });
            })
            ->when($case, function ($query) use ($case): void {
                $experts_who_did_not_accepte_the_case = $case->experts()->where('accepted', CaseExpertStatus::REJECTED->value)->pluck('user_id')->unique()->toArray();
                $query->whereNotIn('id', $experts_who_did_not_accepte_the_case);
            })
            ->when($skip_ids, function ($query) use ($skip_ids): void {
                $query->whereNotIn('id', $skip_ids);
            })
            /* Exlude Justina Rac (270) from the list of available experts, when the contract holder is CGP Europe (2) */
            ->when(
                $org_data?->contract_holder_id && $org_data->contract_holder_id === 2,
                fn ($query) => $query->where('id', '<>', 270)
            )
            ->orderBy('name', 'asc')
            ->get();

        /**
         * It the:
         *
         * - company is Nestlé (227)
         * - country is Switzerland (5)
         *
         * then filter experts from Poland(2) and Romania(6)
         */
        if ($company_id === 227 && $country_id === 5) {
            $experts = $experts->filter(fn ($expert): bool => ! in_array([2, 6], $expert->expertCountries()->withoutGlobalScope(CountryScope::class)->pluck('countries.id')->toArray()));
        }

        /**
         * If the:
         *
         * - company is Tesco Slovakia(1254)
         *
         * then remove Pavol Gurican - Ksebe (1520) from availbale experts
         */
        if ($company_id === 1254) {
            $experts = $experts->reject(fn (User $expert): bool => $expert->id === 1520);
        }

        /**
         * If the:
         *
         * - company is Inditex Ukraine(1362)
         *
         * then remove Tsarynnyk Liudmyla (911) from availbale experts
         */
        if ($company_id === 1362) {
            $experts = $experts->reject(fn (User $expert): bool => $expert->id === 911);
        }

        /**
         * If the:
         *
         * - company is LPP SA (843)
         * - country is Slovenia (32)
         *
         * then remove Crtomir Casar (1519) from availbale experts
         */
        if ($company_id === 843 && $country_id === 32) {
            $experts = $experts->reject(fn (User $expert): bool => $expert->id === 1519);
        }

        /**
         * If the:
         *
         * - consultation is not personal
         * - country is Poland (2)
         *
         * then remove Martyna Jasińska (1013) from availbale experts
         */
        if (! $is_personal && $country_id === 2) {
            $experts = $experts->reject(fn (User $expert): bool => $expert->id === 1013);
        }

        /**
         * If the:
         *
         * - company is Deloitte Romania(571) OR Deloitte Poland(572)
         * - permission is psychological (1)
         *
         * then return only experts under 40 EUR hourly rate
         */
        if (in_array($company_id, [571, 572]) && $permission_id === 1) {
            $experts = $experts->filter(function (User $expert) use ($specialization_id, $language_skill_id): bool {
                if (! $expert->expert_data || ! $expert->invoice_datas || ! $expert->specializations->count() || ! $expert->language_skills->count() && ! $expert->expert_data->native_language) {
                    return false;
                }

                $has_specialization = in_array($specialization_id, $expert->specializations->pluck('id')->toArray());
                $has_language_skill = in_array($language_skill_id, $expert->language_skills->pluck('id')->toArray());
                $has_native_language = $expert->expert_data->native_language == $language_skill_id;
                $is_under_40_eur = $expert->invoice_datas->hourly_rate_50 <= 40;

                return $is_under_40_eur && $has_specialization && ($has_language_skill || $has_native_language);

            });

            return $experts->sortBy(fn (User $expert): int => optional($expert->invoice_datas)->hourly_rate_50)->take(1);
        }

        /**
         * If the:
         *
         * - company is Deloitte Slovakia(573) OR Deloitte Czech Republic(574)
         * - permission is psychological (1)
         *
         * then return only experts under 55 EUR hourly rate
         */
        if (in_array($company_id, [573, 574]) && $permission_id === 1) {
            $experts = $experts->filter(function (User $expert) use ($specialization_id, $language_skill_id): bool {
                if (! $expert->expert_data || ! $expert->invoice_datas || ! $expert->specializations->count() || ! $expert->language_skills->count() && ! $expert->expert_data->native_language) {
                    return false;
                }

                $has_specialization = in_array($specialization_id, $expert->specializations->pluck('id')->toArray());
                $has_language_skill = in_array($language_skill_id, $expert->language_skills->pluck('id')->toArray());
                $has_native_language = $expert->expert_data->native_language == $language_skill_id;
                $is_under_55_eur = $expert->invoice_datas->hourly_rate_50 <= 55;

                return $is_under_55_eur && $has_specialization && ($has_language_skill || $has_native_language);

            });

            return $experts->sortBy(fn (User $expert): int => optional($expert->invoice_datas)->hourly_rate_50)->take(1);
        }

        if ($is_crisis && $permission_id === 1) {
            return $experts->filter(fn ($expert): bool => optional($expert->expert_data)->crisis_psychologist && in_array($language_skill_id, $expert->language_skills->pluck('id')->toArray()));
        }

        if ($permission_id === 1) {
            $cgp_experts = $experts->filter(function ($expert) use ($specialization_id, $language_skill_id): bool {
                if (! $expert->expert_data || ! $expert->specializations->count() || ! $expert->language_skills->count() && ! $expert->expert_data->native_language) {
                    return false;
                }

                $is_cgp_employee = optional($expert->expert_data)->is_cgp_employee;
                $has_specialization = in_array($specialization_id, $expert->specializations->pluck('id')->toArray());
                $has_language_skill = in_array($language_skill_id, $expert->language_skills->pluck('id')->toArray());
                $has_native_language = $expert->expert_data->native_language == $language_skill_id;

                return $is_cgp_employee && $has_specialization && ($has_language_skill || $has_native_language);
            });

            if ($cgp_experts->count()) {
                $cgp_experts = $cgp_experts->sortBy(fn ($expert) => Cases::query()
                    ->whereNotIn('status', ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed', 'opened'])
                    ->whereHas('experts', fn (Builder $query) => $query->where('user_id', $expert->id)->whereNotIn('accepted', [CaseExpertStatus::REJECTED->value]))
                    ->get()->filter(fn ($case): bool => $case->experts()->first()->id == $expert->id)->count());

                return $cgp_experts->take(1);
            }
        }

        $other_experts = $experts->filter(function ($expert) use ($specialization_id, $language_skill_id, $consultation_minute, $permission_id): bool {
            $is_cgp_employee = optional($expert->expert_data)->is_cgp_employee;
            $has_invoce_data = $expert->invoice_datas && ! empty($expert->invoice_datas->currency);
            $has_language_skill = in_array($language_skill_id, $expert->language_skills->pluck('id')->toArray());
            $has_native_language = optional($expert->expert_data)->native_language == $language_skill_id;

            if ($permission_id === 1) {
                if (! $expert->expert_data || ! $expert->specializations->count() || ! $expert->language_skills->count() && ! $expert->expert_data->native_language || ! $consultation_minute) {
                    return false;
                }

                $has_specialization = in_array($specialization_id, $expert->specializations->pluck('id')->toArray());

                return ! $is_cgp_employee && $has_specialization && ($has_language_skill || $has_native_language) && $has_invoce_data;
            }

            return $has_invoce_data && ($has_language_skill || $has_native_language);
        });

        $other_experts = $other_experts->map(function ($expert) use ($consultation_minute): array {
            // if expert has custom or fixed invoicing type, push to the top of the list
            if ($expert->invoice_datas->invoicing_type === InvoicingType::TYPE_CUSTOM || $expert->invoice_datas->invoicing_type === InvoicingType::TYPE_FIXED) {
                return ['expert' => $expert, 'price' => $expert->invoice_datas->ranking_hourly_rate ?? 0];
            }

            // 116 is the case input id of 50 minute consultation, 117 is the case input id of 60 minute consultation
            if ($consultation_minute === 116 || $consultation_minute === 117) {
                $expert_price = (int) str_replace(' ', '', (string) $expert->invoice_datas->hourly_rate_50);
            } else {
                $expert_price = (int) str_replace(' ', '', (string) $expert->invoice_datas->hourly_rate_30);
            }

            $expert_currency = $expert->invoice_datas->currency;

            $converter = new CurrencyCached(60 * 60 * 24);

            return ['expert' => $expert, 'price' => $converter->convert($expert_price, 'EUR', strtoupper($expert_currency))];
        })->sortBy('price');

        if ($other_experts->count()) {
            return $other_experts
                ->filter(fn ($expert): bool => $expert['price'] == $other_experts->first()['price'])
                ->map(fn ($expert) => $expert['expert'])
                ->sortBy(fn ($expert) => Cases::query()
                    ->whereNotIn('status', ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed', 'opened'])
                    ->whereHas('experts', fn (Builder $query) => $query->where('user_id', $expert->id)->whereNotIn('accepted', [CaseExpertStatus::REJECTED->value]))
                    ->get()->filter(fn ($case): bool => $case->case_accepted_expert()->id == $expert->id)->count())
                ->take(1);
        }

        $experts = $experts->filter(function ($expert) use ($language_skill_id, $ignore_language_skill): bool {
            if (! $expert->language_skills->count()) {
                return false;
            }

            $has_language_skill = ($ignore_language_skill) ? true : in_array($language_skill_id, $expert->language_skills->pluck('id')->toArray());
            $has_native_language = ($ignore_language_skill) ? true : optional($expert->expert_data)->native_language == $language_skill_id;

            return ! optional($expert->expert_data)->is_cgp_employee && $expert->invoice_datas && ! empty($expert->invoice_datas->currency) && ($has_language_skill || $has_native_language);
        })->sortBy(function ($expert) use ($consultation_minute): float {
            // if expert has custom or fixed invoicing type, push to the top of the list
            if ($expert->invoice_datas->invoicing_type === InvoicingType::TYPE_CUSTOM || $expert->invoice_datas->invoicing_type === InvoicingType::TYPE_FIXED) {
                return 0;
            }

            // 116 is the case input id of 50 minute consultation, 117 is the case input id of 60 minute consultation
            if ($consultation_minute === 116 || $consultation_minute === 117) {
                $expert_price = (int) str_replace(' ', '', (string) $expert->invoice_datas->hourly_rate_50);
            } else {
                $expert_price = (int) str_replace(' ', '', (string) $expert->invoice_datas->hourly_rate_30);
            }

            $expert_currency = $expert->invoice_datas->currency;

            $converter = new CurrencyCached(60 * 60 * 24);

            return $converter->convert($expert_price, 'EUR', strtoupper($expert_currency));
        });

        return $experts->take(5);
    }
}

if (! function_exists('has_invoicing_opened')) {
    function has_invoicing_opened(?User $user = null): bool
    {
        if ($user === null) {
            return false;
        }
        $opened_invoicing = $user->opened_invoicing;

        if (empty($opened_invoicing)) {
            return false;
        }

        return ! Carbon::now()->gt(Carbon::parse($opened_invoicing->until));
    }
}

if (! function_exists('get_invoice_net_total')) {
    function get_invoice_net_total(array $direct_invoice_data): int|float
    {
        if (! array_key_exists('workshop_datas', $direct_invoice_data)) {
            $direct_invoice_data['workshop_datas'] = [];
        }

        if (! array_key_exists('crisis_datas', $direct_invoice_data)) {
            $direct_invoice_data['crisis_datas'] = [];
        }

        if (! array_key_exists('other_activity_datas', $direct_invoice_data)) {
            $direct_invoice_data['other_activity_datas'] = [];
        }

        if (! array_key_exists('optum_closed_cases_datas', $direct_invoice_data)) {
            $direct_invoice_data['optum_closed_cases_datas'] = [];
        }

        if (! array_key_exists('compsych_closed_cases_datas', $direct_invoice_data)) {
            $direct_invoice_data['compsych_closed_cases_datas'] = [];
        }

        $workshop_total = (int) collect($direct_invoice_data['workshop_datas'])->sum('price');
        $crisis_total = (int) collect($direct_invoice_data['crisis_datas'])->sum('price');
        $other_activity_total = (int) collect($direct_invoice_data['other_activity_datas'])->sum('price');
        $invoice_items_total = collect($direct_invoice_data['invoice_items'])
            ->whereIn('input', [
                InvoiceItem::INPUT_TYPE_AMOUNT,
                InvoiceItem::INPUT_TYPE_MULTIPLICATION,
                InvoiceItem::INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS,
                InvoiceItem::INPUT_TYPE_OPTUM_LAW_CONSULTATIONS,
                InvoiceItem::INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS,
                InvoiceItem::INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS,
                InvoiceItem::INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS,
                InvoiceItem::INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS,
                InvoiceItem::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_15,
                InvoiceItem::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_30,
            ])
            ->sum(function (array $item): int|float {
                if (is_null($item['amount']) && is_null($item['volume'])) {
                    return 0;
                }

                $amount = str_replace(' ', '', (string) $item['amount']['value']);

                // $amount = substr($item['amount']['value'], 0, strpos($item['amount']['value'], '.'));

                if (array_key_exists('volume', $item) && ! empty($item['volume'])) {
                    $volume = str_replace(' ', '', (string) $item['volume']['value']);

                    return (float) $amount * (float) $volume;
                }

                return (float) $amount;
            });

        return $workshop_total + $crisis_total + $other_activity_total + $invoice_items_total;
    }
}

if (! function_exists('currency_change_documnet_missing')) {
    function currency_change_documnet_missing(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (optional($user)->type !== 'expert') {
            return false;
        }

        if (optional($user->expert_data)->is_cgp_employee) {
            return false;
        }

        if (in_array(strtolower((string) $user->invoice_datas->currency), ['eur', 'usd', 'chf', 'huf'])) {
            return false;
        }

        if ($user->invoice_datas->invoicing_type !== InvoicingType::TYPE_NORMAL) {
            return false;
        }

        if ($user->has_missing_expert_data()) {
            return false;
        }

        if (($currency_change = $user->expert_currency_changes) === null) {
            return true;
        }

        if (empty($currency_change->downloaded_at)) {
            return true;
        }

        return empty($currency_change->document);
    }
}

if (! function_exists('show_currency_change_menu')) {
    function show_currency_change_menu(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (optional($user)->type !== 'expert' || ! optional($user)->invoice_datas) {

            return false;
        }

        if (optional($user->expert_data)->is_cgp_employee) {
            return false;
        }

        if (in_array(strtolower((string) $user->invoice_datas->currency), ['eur', 'usd', 'chf', 'huf'])) {
            return false;
        }

        if ($user->invoice_datas->invoicing_type !== InvoicingType::TYPE_NORMAL) {
            return false;
        }

        return ! $user->has_missing_expert_data();
    }
}

if (! function_exists('company_quarter_riport_active')) {
    function company_quarter_riport_active(Company $company, int $quarter): bool
    {
        // Return false for current(stll in progress) and further quarters if its not the first quarter of the year
        if (Carbon::now()->quarter != 1 && $quarter >= Carbon::now()->quarter && config('app.env') === 'production') {
            return false;
        }

        $dates = QuarterDates::dates($quarter);
        $period = CarbonPeriod::create($dates[0], '1 month', $dates[1]);

        $inactive_riports = collect([]);
        collect($period)->each(function ($date) use ($company, &$inactive_riports): void {
            $riport = $company->riports->where('from', '>=', $date->startOfMonth())->where('to', '<=', $date->endOfMonth())->first();
            if (! $riport || ! $riport->is_active) {
                $inactive_riports->push(optional($riport)->id);
            }
        });

        return $inactive_riports->isEmpty();
    }
}

if (! function_exists('has_connected_companies')) {
    function has_connected_companies(User $user): bool
    {
        $company = $user->companies()->first();

        if (! $company) {
            return false;
        }

        return $company->get_connected_companies()->count() > 1;
    }
}

if (! function_exists('company_monthly_riport_active')) {
    function company_monthly_riport_active(Company $company, int $quarter): array
    {
        $dates = QuarterDates::dates($quarter);
        $period = CarbonPeriod::create($dates[0], '1 month', $dates[1]);

        $riports = collect([]);

        collect($period)->each(function ($date) use ($company, &$riports): void {
            $riport = $company->riports->where('from', '>=', $date->startOfMonth())->where('to', '<=', $date->endOfMonth())->first();
            if ($riport !== null) {
                $riports->push(['date' => $riport->from->format('Y.m.d').'-'.$riport->to->format('Y.m.d'), 'active' => $riport->is_active]);
            } else {
                $riports->push(['date' => $date->startOfMonth()->format('Y.m.d').'-'.$date->endOfMonth()->format('Y.m.d'), 'active' => false]);
            }
        });

        return $riports->toArray();
    }
}

if (! function_exists('has_access_to_activity_plan')) {
    function has_access_to_activity_plan(): bool
    {
        $user = auth()->user();

        if ($user === null) {
            return false;
        }
        /**
         * Allow access to activity plan if:
         * - user is admin
         * - user is connected to a company
         * - user is Kiss Barbara (1204)
         */
        if (ActivityPlan::query()->where('user_id', $user->id)->exists()) {
            return true;
        }
        if ($user->type === 'admin') {
            return true;
        }

        return $user->id === 1204;
    }
}

if (! function_exists('has_super_access_to_activity_plan')) {
    function has_super_access_to_activity_plan(): bool
    {
        /**
         * Allow access to activity plan if:
         * - user is admin
         * - user is Kiss Barbara (1204)
         */

        return auth()->user()->type === 'admin' || auth()->user()->id === 1204;
    }
}

if (! function_exists('get_in_progress_cases_count')) {
    function get_in_progress_cases_count(int $company_id, int $country_id, ?int $year, int $quarter): int
    {
        if (! $year) {
            $year = (Carbon::now()->quarter === 1) ? Carbon::now()->subQuarter()->year : Carbon::now()->year;
        }

        $end_of_quater = Carbon::now()->setYear($year)->startOfYear()->addMonths($quarter * 3 - 3)->endOfQuarter();

        $last_riport_of_quarter = Riport::query()
            ->where('company_id', $company_id)
            ->where('to', $end_of_quater->format('Y-m-d'))
            ->first();

        if (! $last_riport_of_quarter) {
            return 0;
        }

        return $last_riport_of_quarter->values
            ->where('country_id', $country_id)
            ->where('type', RiportValue::TYPE_STATUS)
            ->whereIn('value', ['opened', 'assigned_to_expert', 'employee_contacted'])
            ->where('is_ongoing', 1)
            ->count();
    }
}
