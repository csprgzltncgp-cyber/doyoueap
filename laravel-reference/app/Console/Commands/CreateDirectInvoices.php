<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\DirectBillingData;
use App\Models\DirectInvoice;
use App\Models\DirectInvoiceCrisisData;
use App\Models\DirectInvoiceData;
use App\Models\DirectInvoiceOtherActivityData;
use App\Models\DirectInvoiceWorkshopData;
use App\Models\Envelope;
use App\Models\InvoiceItem;
use App\Traits\DirectInvoicesTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateDirectInvoices extends Command
{
    use DirectInvoicesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:direct-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Direct Invoices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][CreateDirectInvoices]: fired!');

        // DirectInvoiceWorkshopData::query()->truncate();
        // CompletionCertificate::query()->truncate();
        // Envelope::query()->truncate();
        // DirectInvoice::query()->truncate();

        $companies_with_one_countries = Company::query()
            ->where('active', true)
            ->whereHas('country_differentiates', fn ($query) => $query->where('invoicing', false))
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 2))
            // ->whereIn('id', [717])
            ->get();

        foreach ($companies_with_one_countries as $company) {
            $this->create_direct_invoices($company);
        }

        $companies_with_more_countries = Company::query()->with(['countries'])
            ->where('active', true)
            ->whereHas('country_differentiates', fn ($query) => $query->where('invoicing', true))
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', 2))
            // ->whereIn('id', [717])
            ->get();

        foreach ($companies_with_more_countries as $company) {
            foreach ($company->countries as $country) {
                $this->create_direct_invoices($company, $country->id);
            }
        }

        return self::SUCCESS;
    }

    private function create_direct_invoices($company, $country_id = null): void
    {
        // Get company contract date (start)
        $contract_date = optional($company->org_datas()
            ->when($country_id, fn ($query) => $query->where('country_id', $country_id))
            ->first())->contract_date;

        // If contract start date is set and is later than the current date, than skip generating direct invoice data for the company
        if ($contract_date && Carbon::parse($contract_date)->gte(Carbon::now())) {
            return;
        }

        $direct_invoice_datas = DirectInvoiceData::query()->with(['direct_billing_data'])->where(['company_id' => $company->id, 'country_id' => $country_id])->get();

        $direct_invoice_datas = $this->filter_by_frequency($direct_invoice_datas);

        if ($direct_invoice_datas->count() > 1) {
            foreach ($direct_invoice_datas as $direct_invoice_data) {
                $this->save_direct_invoice_data($company, $country_id, $direct_invoice_data->id);
            }
        } else {
            $this->save_direct_invoice_data($company, $country_id);
        }
    }

    private function save_direct_invoice_data($company, $country_id, $direct_invoice_data_id = null): void
    {
        $data = collect([]);
        $direct_invoice = new DirectInvoice;
        $direct_invoice->company_id = $company->id;
        $direct_invoice->country_id = $country_id;
        $direct_invoice->direct_invoice_data_id = $direct_invoice_data_id;

        $direct_invoice_data = DirectInvoiceData::query()->with(['direct_billing_data'])->where(['company_id' => $company->id, 'country_id' => $country_id])->first();

        if (empty($direct_invoice_data->direct_billing_data)) {
            return;
        }

        if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_QUARTELY && Carbon::now()->subMonthNoOverflow()->month % 3 !== 0) {
            return;
        }

        if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_YEARLY && Carbon::now()->subMonthNoOverflow()->month !== 12) {
            return;
        }

        // $direct_invoice->save();

        $invoice_data = $company->direct_invoice_datas()
            ->where(['country_id' => $country_id])
            ->when(! empty($direct_invoice_data_id), fn ($query) => $query->where('id', $direct_invoice_data_id))
            ->first();

        if (empty($invoice_data)) {
            return;
        }

        if ($invoice_data->is_po_number_changing) {
            $invoice_data->po_number = null;
        }

        $data->put(
            'invoice_data',
            $invoice_data->toArray()
        );

        $billng_data = $company->direct_billing_datas()
            ->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id])
            ->first();

        if (empty($billng_data)) {
            return;
        }

        $data->put(
            'billing_data',
            $billng_data->toArray()
        );

        $invoice_items = $company->invoice_items()
            ->with(['volume', 'amount'])
            ->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id])
            ->get();

        $data->put(
            'invoice_items',
            $invoice_items->toArray()
        );

        $data->put(
            'invoice_comments',
            $company->invoice_comments()
                ->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id])
                ->get()
                ->toArray()
        );

        $data->put(
            'invoice_notes',
            $company->invoice_notes()
                ->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id])
                ->get()
                ->toArray()
        );

        $direct_invoice->from = Carbon::now()->subMonthsNoOverflow((int) $billng_data->billing_frequency)->startOfMonth();
        $direct_invoice->to = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        // Check if company inactive invoicing is set for the current period
        // IF yes, mark the direct invoice as inactive (active = false)
        if ($invoice_data->invoicing_inactive
        && (Carbon::parse($direct_invoice->from)->between($invoice_data->invoicing_inactive_from, $invoice_data->invoicing_inactive_to))
        || Carbon::parse($direct_invoice->to)->between($invoice_data->invoicing_inactive_from, $invoice_data->invoicing_inactive_to)) {
            $direct_invoice->active = false;
        }

        $workshop_datas = collect([]);
        $crisis_datas = collect([]);
        $other_activity_datas = collect([]);

        // Workshop data
        if ($company->invoice_items()->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_WORKSHOP])->exists()) {
            $workshop_datas = DirectInvoiceWorkshopData::query()
                ->with(['workshop'])
                ->where('company_id', $company->id)
                ->where('country_id', $country_id ?? $company->countries->first()->id)
                ->whereNull('direct_invoice_id')
                ->where(function ($query): void {
                    $query->whereDate('invoiceable_after', '<=', now()->endOfMonth())
                        ->orWhereNull('invoiceable_after');
                })
                ->whereDate('created_at', '<=', $direct_invoice->to)
                ->get();

            $formatted_datas = $this->format_workshop_datas($workshop_datas, $billng_data, $direct_invoice);

            $data->put('workshop_datas', $formatted_datas);
        }

        // Crisis data
        if ($company->invoice_items()->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_CRISIS])->exists()) {
            $crisis_datas = DirectInvoiceCrisisData::query()
                ->with(['crisis'])
                ->where(['company_id' => $company->id, 'country_id' => $country_id ?? $company->countries->first()->id])
                ->whereNull('direct_invoice_id')
                ->whereDate('created_at', '<=', $direct_invoice->to)
                ->get();

            $formatted_datas = $this->format_crisis_datas($crisis_datas, $billng_data, $direct_invoice);

            $data->put('crisis_datas', $formatted_datas);
        }

        // Other Activity data
        if ($company->invoice_items()->where(['country_id' => $country_id, 'direct_invoice_data_id' => $invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY])->exists()) {
            $other_activity_datas = DirectInvoiceOtherActivityData::query()
                ->with(['other_activity'])
                ->where(['company_id' => $company->id, 'country_id' => $country_id ?? $company->countries->first()->id])
                ->whereNull('direct_invoice_id')
                ->whereDate('created_at', '<=', $direct_invoice->to)
                ->get();

            $formatted_datas = $this->format_other_activity_datas($other_activity_datas, $billng_data, $direct_invoice);

            $data->put('other_activity_datas', $other_activity_datas);
        }

        $direct_invoice->data = $data->toArray();

        // check if direct invoice already exists
        $existing_direct_invoice = DirectInvoice::query()
            ->where('company_id', $direct_invoice->company_id)
            ->where('country_id', $direct_invoice->country_id)
            ->where('direct_invoice_data_id', $direct_invoice->direct_invoice_data_id)
            ->whereDate('from', $direct_invoice->from)
            ->whereDate('to', $direct_invoice->to)
            ->first();

        if (! empty($existing_direct_invoice)) {
            return;
        }

        $direct_invoice->save();

        foreach ($workshop_datas as $workshop_data) {
            $workshop_data->update(['direct_invoice_id' => $direct_invoice->id]);
        }

        foreach ($crisis_datas as $crisis_data) {
            $crisis_data->update(['direct_invoice_id' => $direct_invoice->id]);
        }

        foreach ($other_activity_datas as $other_activity_data) {
            $other_activity_data->update(['direct_invoice_id' => $direct_invoice->id]);
        }
    }
}
