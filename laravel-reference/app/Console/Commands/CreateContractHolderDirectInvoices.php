<?php

namespace App\Console\Commands;

use App\Enums\ContractHolderCompany;
use App\Models\Company;
use App\Models\DirectBillingData;
use App\Models\DirectInvoice;
use App\Models\DirectInvoiceCrisisData;
use App\Models\DirectInvoiceData;
use App\Models\DirectInvoiceOtherActivityData;
use App\Models\DirectInvoiceWorkshopData;
use App\Models\InvoiceItem;
use App\Models\Scopes\ContractHolderCompanyScope;
use App\Traits\DirectInvoicesTrait;
use App\Traits\InvoiceHelper\ContractHolderTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateContractHolderDirectInvoices extends Command
{
    use ContractHolderTrait;
    use DirectInvoicesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:contract-holder-direct-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create contract holder direct invoices';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Log::info('[COMMAND][ContractHolderDirectInvoices]: fired!');

        $companies = Company::query()
            ->withoutGlobalScope(ContractHolderCompanyScope::class)
            ->whereIn('id', array_column(ContractHolderCompany::cases(), 'value'))
            ->get();

        if (Carbon::now()->day === Carbon::now()->endOfMonth()->subDay()->day) {
            $this->create_direct_invoices($companies->where('id', ContractHolderCompany::LIFEWORKS->value)->first());
        } else {
            foreach ($companies as $company) {
                $this->create_direct_invoices($company);
            }
        }

        return Command::SUCCESS;
    }

    private function create_direct_invoices(Company $company): void
    {
        $direct_invoice_datas = DirectInvoiceData::query()->with('direct_billing_data')->where('company_id', $company->id)->get();

        $direct_invoice_datas = $this->filter_by_frequency($direct_invoice_datas);

        if (Carbon::now()->day === Carbon::now()->endOfMonth()->subDay()->day) {
            $direct_invoice_datas = $direct_invoice_datas->first();

            $direct_invoice_datas = collect([$direct_invoice_datas]);
        }

        foreach ($direct_invoice_datas as $direct_invoice_data) {
            $this->save_direct_invoice($company, $direct_invoice_data);
        }
    }

    private function save_direct_invoice(Company $company, DirectInvoiceData $direct_invoice_data): void
    {
        $data = collect([]);

        $contract_holder_id = match ($company->id) {
            ContractHolderCompany::LIFEWORKS->value => 1,
            ContractHolderCompany::COMPSYCH->value => 3,
            ContractHolderCompany::OPTUM->value => 4,
            ContractHolderCompany::PULSO->value => 5,
            default => 1,
        };

        $contract_holder_company_ids = Company::query()
            ->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', $contract_holder_id))
            ->pluck('id')->toArray();

        $direct_invoice = new DirectInvoice;
        $direct_invoice->company_id = $company->id;
        $direct_invoice->country_id = null;
        $direct_invoice->direct_invoice_data_id = $direct_invoice_data->id;

        if (empty($direct_invoice_data->direct_billing_data)) {
            return;
        }

        if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_QUARTELY && Carbon::now()->subMonthNoOverflow()->month % 3 !== 0) {
            return;
        }

        if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_YEARLY && Carbon::now()->subMonthNoOverflow()->month !== 12) {
            return;
        }

        if ($direct_invoice_data->is_po_number_changing) {
            $direct_invoice_data->po_number = '';
        }

        $data->put(
            'invoice_data',
            $direct_invoice_data->toArray(),
        );

        $data->put(
            'billing_data',
            $direct_invoice_data->direct_billing_data->toArray(),
        );

        $data->put(
            'invoice_items',
            $company->invoice_items()
                ->with(['volume', 'amount'])
                ->where('direct_invoice_data_id', $direct_invoice_data->id)
                ->get()
                ->toArray()
        );

        $data->put(
            'invoice_comments',
            $company->invoice_comments()
                ->where('direct_invoice_data_id', $direct_invoice_data->id)
                ->get()
                ->toArray()
        );

        $data->put(
            'invoice_notes',
            $company->invoice_notes()
                ->where('direct_invoice_data_id', $direct_invoice_data->id)
                ->get()
                ->toArray()
        );

        if (Carbon::now()->day === Carbon::now()->endOfMonth()->subDay()->day) {
            $direct_invoice->from = Carbon::now()->subMonthsNoOverflow($direct_invoice_data->direct_billing_data->billing_frequency - 1)->startOfMonth();
            $direct_invoice->to = Carbon::now()->endOfMonth();
        } else {
            $direct_invoice->from = Carbon::now()->subMonthsNoOverflow($direct_invoice_data->direct_billing_data->billing_frequency)->startOfMonth();
            $direct_invoice->to = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        }

        $workshop_datas = collect([]);
        $crisis_datas = collect([]);
        $other_activity_datas = collect([]);

        // Workshop Data
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_WORKSHOP])->exists()) {
            $workshop_datas = DirectInvoiceWorkshopData::query()
                ->with(['workshop'])
                ->whereIn('company_id', $contract_holder_company_ids)
                ->whereNull('direct_invoice_id')
                ->where(function ($query): void {
                    $query->whereDate('invoiceable_after', '<=', now()->endOfMonth())
                        ->orWhereNull('invoiceable_after');
                })
                ->whereDate('created_at', '>=', $direct_invoice->from)
                ->whereDate('created_at', '<=', $direct_invoice->to)
                ->get();

            $formatted_datas = $this->format_workshop_datas($workshop_datas, $direct_invoice_data->direct_billing_data, $direct_invoice);

            $data->put('workshop_datas', $formatted_datas);
        }

        // Crisis Data
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_CRISIS])->exists()) {
            $crisis_datas = DirectInvoiceCrisisData::query()
                ->with(['crisis'])
                ->whereIn('company_id', $contract_holder_company_ids)
                ->whereNull('direct_invoice_id')
                ->whereDate('created_at', '>=', $direct_invoice->from)
                ->whereDate('created_at', '<=', $direct_invoice->to)
                ->get();

            $formatted_datas = $this->format_crisis_datas($crisis_datas, $direct_invoice_data->direct_billing_data, $direct_invoice);

            $data->put('crisis_datas', $formatted_datas);
        }

        // Other Activity Data
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY])->exists()) {
            $other_activity_datas = DirectInvoiceOtherActivityData::query()
                ->with(['other_activity'])
                ->whereIn('company_id', $contract_holder_company_ids)
                ->whereNull('direct_invoice_id')
                ->whereDate('created_at', '>=', $direct_invoice->from)
                ->whereDate('created_at', '<=', $direct_invoice->to)
                ->get();

            $formatted_datas = $this->format_other_activity_datas($other_activity_datas, $direct_invoice_data->direct_billing_data, $direct_invoice);

            $data->put('other_activity_datas', $formatted_datas);
        }

        // Optum psychology consultations
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS])->exists()) {
            $consultations_number = $this->get_optum_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                1 // 1 is the permission id for psychology
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_OPTUM_PSYCHOLOGY_CONSULTATIONS) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Optum law consultations
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_OPTUM_LAW_CONSULTATIONS])->exists()) {
            $consultations_number = $this->get_optum_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                2 // 2 is the permission id for law
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_OPTUM_LAW_CONSULTATIONS) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Optum finance consultations
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS])->exists()) {
            $consultations_number = $this->get_optum_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                3 // 3 is the permission id for finance
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_OPTUM_FINANCE_CONSULTATIONS) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Compsych psychology consultations
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS])->exists()) {
            $consultations_number = $this->get_compsych_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                1 // 1 is the permission id for psychology
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_COMPSYCH_PSYCHOLOGY_CONSULTATIONS) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Compsych well being coaching consultations (30 min)
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_30])->exists()) {
            $consultations_number = $this->get_compsych_well_being_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                true
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_30) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Compsych well being coaching consultations (15 min)
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_15])->exists()) {
            $consultations_number = $this->get_compsych_well_being_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                false
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_COMPSYCH_WELL_BEING_COACHING_CONSULTATIONS_15) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Compsych law consultations
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS])->exists()) {
            $consultations_number = $this->get_compsych_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                2 // 2 is the permission id for law
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_COMPSYCH_LAW_CONSULTATIONS) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        // Compsych finance consultations
        if ($company->invoice_items()->where(['direct_invoice_data_id' => $direct_invoice_data->id, 'input' => InvoiceItem::INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS])->exists()) {
            $consultations_number = $this->get_compsych_consultations_number(
                $direct_invoice->from,
                $direct_invoice->to,
                3 // 3 is the permission id for finance
            );

            // update the corresponding invoice items
            $data['invoice_items'] = collect($data['invoice_items'])->map(function (array $item) use ($consultations_number): array {
                if ((int) $item['input'] === InvoiceItem::INPUT_TYPE_COMPSYCH_FINANCE_CONSULTATIONS) {
                    $item['volume']['value'] = $consultations_number;
                }

                return $item;
            })->toArray();
        }

        $direct_invoice->data = $data->toArray();

        // check if direct invoice already exists
        $existing_direct_invoice = DirectInvoice::query()
            ->where('company_id', $direct_invoice->company_id)
            ->where('country_id', null)
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
