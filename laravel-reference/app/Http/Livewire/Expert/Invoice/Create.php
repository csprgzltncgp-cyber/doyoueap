<?php

namespace App\Http\Livewire\Expert\Invoice;

use App\Enums\InvoicingType;
use App\Helpers\CurrencyCached;
use App\Mail\LargeExpertInvoiceNotification;
use App\Models\AdditionalInvoiceItem;
use App\Models\Country;
use App\Models\ExpertConsultationCount;
use App\Models\Invoice;
use App\Models\InvoiceData;
use App\Models\InvoiceDataChanges;
use App\Models\Permission;
use App\Scopes\CountryScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $invoiceDatas;

    public $invoiceCaseDatas;

    public $invoiceCaseDatasPeriods;

    public $invoiceWorkshopDatas;

    public $invoiceWorkshopDatasPeriods;

    public $invoiceLiveWebinarDatas;

    public $invoiceLiveWebinarDatasPeriods;

    public $invoiceCrisisDatas;

    public $invoiceCrisisDatasPeriods;

    public $invoiceOtherActivityDatas;

    public $invoiceOtherActivityDatasPeriods;

    public $additionalInvoiceItems;

    public $newAdditionalInvoiceItemName;

    public $newAdditionalInvoiceItemPrice;

    public $invoice;

    public $invoiceDataChanges = [];

    public $hasInternatioanlTaxNumber;

    // when user has invoice cases data without duartion
    public $casesPrice;

    public $grandTotal;

    public $acceptCasesPrice = false;

    public $acceptWorkshopsPrice = false;

    public $acceptLiveWebinarPrice = false;

    public $acceptCrisisPrice = false;

    public $acceptOtherActivityPrice = false;

    public $acceptGrandTotalPrice = false;

    public $uploadedInvoice;

    public $custom_invoice_items;

    protected $messages;

    public $invoiceCaseDatasPeriodsByPermission;

    protected function rules(): array
    {
        $this->messages = [
            'uploadedInvoice' => __('invoice.upload_scanned_invoice_error'),
        ];

        return [
            'invoiceDatas.name' => 'required',
            'invoiceDatas.email' => 'required',
            'invoiceDatas.account_number' => 'nullable',
            'invoiceDatas.swift' => 'nullable',
            'invoiceDatas.tax_number' => 'nullable',
            'invoiceDatas.international_tax_number' => 'nullable',
            'invoiceDatas.bank_name' => 'required',
            'invoiceDatas.bank_address' => 'required',
            'invoiceDatas.destination_country' => 'required',
            'invoiceDatas.currency' => 'required',
            'invoice.date_of_issue' => 'required',
            'invoice.payment_deadline' => 'required',
            'invoice.number' => 'required',
            'uploadedInvoice' => 'required|max:10240',
        ];
    }

    public function mount()
    {
        // Redirect user to their profile page if they have missing required expert data
        if (Auth::user()->has_missing_expert_data()) {
            return redirect()->route('expert.profile', ['missingData' => true]);
        }

        $this->invoiceDatas = InvoiceData::query()->firstOrCreate(['user_id' => Auth::user()->id]);

        $this->hasInternatioanlTaxNumber = (bool) $this->invoiceDatas->international_tax_number;

        $this->invoiceCaseDatas = Auth::user()->invoice_case_datas()
            ->whereNull(['invoice_id'])
            ->whereDate('created_at', '<=', Carbon::now()->subMonthWithNoOverflow()->endOfMonth())
            ->where('consultations_count', '>', 0)
            ->get();

        $this->invoiceCaseDatasPeriods = $this->invoiceCaseDatas->map(fn ($case) => Str::title(Carbon::parse($case->created_at)->translatedFormat('F')))->unique();

        $this->invoiceWorkshopDatas = Auth::user()->invoice_workshop_datas()->whereNull(['invoice_id'])->whereDate('created_at', '<=', Carbon::now()->subMonthWithNoOverflow()->endOfMonth())->get();
        $this->invoiceWorkshopDatasPeriods = $this->invoiceWorkshopDatas->map(fn ($workshop) => Str::title(Carbon::parse($workshop->created_at)->translatedFormat('F')))->unique();

        $this->invoiceCrisisDatas = Auth::user()->invoice_crisis_datas()->whereNull(['invoice_id'])->whereDate('created_at', '<=', Carbon::now()->subMonthWithNoOverflow()->endOfMonth())->get();
        $this->invoiceCrisisDatasPeriods = $this->invoiceCrisisDatas->map(fn ($crisis) => Str::title(Carbon::parse($crisis->created_at)->translatedFormat('F')))->unique();

        $this->invoiceOtherActivityDatas = Auth::user()->invoice_other_activity_datas()->whereNull(['invoice_id'])->get();
        $this->invoiceOtherActivityDatasPeriods = $this->invoiceOtherActivityDatas->map(fn ($otherActivity) => Str::title(Carbon::parse($otherActivity->created_at)->translatedFormat('F')))->unique();

        $this->additionalInvoiceItems = collect([]);
        $this->invoice = new Invoice([
            'user_id' => Auth::user()->id,
            'payment_deadline' => $this->getPaymentDeadline(now()->format('Y-m-d')),
        ]);

        $this->invoiceLiveWebinarDatas = Auth::user()->invoice_live_webinar_datas()->whereNull(['invoice_id'])->get();
        $this->invoiceLiveWebinarDatasPeriods = $this->invoiceLiveWebinarDatas->map(fn ($live_webinar_data) => Str::title(Carbon::parse($live_webinar_data->created_at->subMonthWithNoOverflow())->translatedFormat('F')))->unique();

        $this->custom_invoice_items = auth()->user()->custom_invoice_items;

        return null;
    }

    public function render()
    {
        $countries = Country::query()->withoutGlobalScope(CountryScope::class)->get()->sortBy('code');
        $prices = $this->getPrices();

        $consultation_count = ExpertConsultationCount::query()
            ->where('month', Carbon::parse($this->invoice->date_of_issue)->subMonthNoOverflow()->format('Y-m'))
            ->where('user_id', Auth::user()->id)
            ->first();

        $this->invoiceCaseDatasPeriodsByPermission = $this->invoiceCaseDatas->groupBy('permission_id')
            ->mapWithKeys(fn ($values, $permission_id) => is_null($permission_id) ? [__('invoice.uncategorized') => $values] : [$permission_id => $values])
            ->map(fn ($cases, $permission_id): array => [
                'permission_id' => $permission_id,
                'permission_name' => Permission::query()->find($permission_id)?->translation->value ?? __('invoice.uncategorized'),
                'period' => $cases->map(fn ($case) => Str::title(Carbon::parse($case->created_at)->translatedFormat('F')))->unique(),
            ]);

        $grouppedInvoiceCaseDatas = $this->invoiceCaseDatas->groupBy('permission_id')
            ->mapWithKeys(fn ($values, $permission_id) => is_null($permission_id) ? ['uncategorized' => $values] : [$permission_id => $values]);

        return view('livewire.expert.invoice.create', ['countries' => $countries, 'prices' => $prices, 'consultation_count' => $consultation_count, 'grouppedInvoiceCaseDatas' => $grouppedInvoiceCaseDatas])->extends('layout.master');
    }

    public function updated($propertyName, $value): void
    {
        $this->validateOnly($propertyName);

        if (Str::startsWith($propertyName, 'invoiceDatas')) {
            $key = Str::after($propertyName, 'invoiceDatas.');

            if (! $this->invoiceDatas->wasRecentlyCreated && $this->invoiceDatas->getOriginal($key) != $value && $this->invoiceDatas->getOriginal($key) != null && ! in_array($key, array_column($this->invoiceDataChanges, 'attribute'))) {
                $this->invoiceDataChanges[] = [
                    'attribute' => $key,
                    'user_id' => Auth::user()->id,
                    'invoice_id' => $this->invoice->id,
                ];
            }

            $this->invoiceDatas->update([
                $key => $value,
            ]);
        }
    }

    public function updatedInvoiceDateOfIssue(): void
    {
        $this->validateOnly('invoice.date_of_issue');

        $this->invoice->payment_deadline = $this->getPaymentDeadline($this->invoice->date_of_issue);
    }

    public function addAdditionalInvoieItem(): void
    {
        if (empty($this->newAdditionalInvoiceItemName) || empty($this->newAdditionalInvoiceItemPrice)) {
            return;
        }

        $this->additionalInvoiceItems->push([
            'name' => $this->newAdditionalInvoiceItemName,
            'price' => $this->newAdditionalInvoiceItemPrice,
        ]);

        $this->newAdditionalInvoiceItemName = '';
        $this->newAdditionalInvoiceItemPrice = '';
    }

    public function removeAdditionalInvoieItem($index): void
    {
        $this->additionalInvoiceItems->forget($index);
    }

    public function removeUploadedInvoice(): void
    {
        $this->uploadedInvoice = null;
    }

    public function store()
    {
        // validate everything and check if user accepted prices
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return null;
        }

        if ($this->invoiceCaseDatas->count() > 0 && ! $this->acceptCasesPrice && empty($this->casesPrice)
        && $this->invoiceDatas->invoicing_type !== InvoicingType::TYPE_CUSTOM) {
            $this->emit('errorEvent', __('invoice.accept_case_price_error'));

            return null;
        }

        if ($this->invoiceWorkshopDatas->count() > 0 && ! $this->acceptWorkshopsPrice
        && $this->invoiceDatas->invoicing_type !== InvoicingType::TYPE_CUSTOM) {
            $this->emit('errorEvent', __('invoice.accept_workshop_price_error'));

            return null;
        }

        if ($this->invoiceCrisisDatas->count() > 0 && ! $this->acceptCrisisPrice
        && $this->invoiceDatas->invoicing_type !== InvoicingType::TYPE_CUSTOM) {
            $this->emit('errorEvent', __('invoice.accept_crisis_price_error'));

            return null;
        }

        if ($this->invoiceOtherActivityDatas->count() > 0 && ! $this->acceptOtherActivityPrice
        && $this->invoiceDatas->invoicing_type !== InvoicingType::TYPE_CUSTOM) {
            $this->emit('errorEvent', __('invoice.accept_other_activity_price_error'));

            return null;
        }

        if ($this->invoiceLiveWebinarDatas->count() > 0 && ! $this->acceptLiveWebinarPrice
        && $this->invoiceDatas->invoicing_type !== InvoicingType::TYPE_CUSTOM) {
            $this->emit('errorEvent', __('invoice.accept_live_webinar_price_error'));

            return null;
        }

        if (! $this->acceptGrandTotalPrice) {
            $this->emit('errorEvent', __('invoice.accept_grand_total_price_error'));

            return null;
        }

        // save invoice model
        $prices = $this->getPrices();

        // IF invoicing type is fixed, set case prices to fixed wage
        if ($this->invoiceDatas->invoicing_type === InvoicingType::TYPE_FIXED) {
            $prices['casesPrice'] = $this->invoiceDatas->fixed_wage;
        }

        // IF invoicing type is custom, set prices to 0
        if ($this->invoiceDatas->invoicing_type === InvoicingType::TYPE_CUSTOM) {
            $prices['workshopPrice'] = 0;
            $prices['crisisPrice'] = 0;
            $prices['otherActivityPrice'] = 0;
            $prices['casesPrice'] = 0;
            $prices['liveWebinarPrice'] = 0;
        }

        $this->invoice->fill(
            array_merge(
                collect($this->invoiceDatas->toArray())->except(['id', 'created_at', 'updated_at', 'hourly_rate_50', 'hourly_rate_30', 'hourly_rate_15', 'fixed_wage', 'invoicing_type', 'ranking_hourly_rate', 'single_session_rate'])->toArray(),
                [
                    'status' => 'created',
                    'workshop_total' => (int) str_replace(' ', '', (string) $prices['workshopPrice']),
                    'crisis_total' => (int) str_replace(' ', '', (string) $prices['crisisPrice']),
                    'other_activity_total' => (int) str_replace(' ', '', (string) $prices['otherActivityPrice']),
                    'live_webinar_total' => (int) str_replace(' ', '', (string) $prices['liveWebinarPrice']),
                    'cases_total' => (int) str_replace(' ', '', (string) $prices['casesPrice']),
                    'grand_total' => (int) str_replace(' ', '', (string) $prices['grandTotal']),
                ]
            )
        );

        $this->invoice->save();

        // save uploaded invoice
        $this->invoice->uploadInvoice($this->uploadedInvoice[0]);

        // save invoice data changes
        foreach ($this->invoiceDataChanges as $change) {
            $change['invoice_id'] = $this->invoice->id;
            InvoiceDataChanges::query()->create($change);
        }

        // save additional invoice items
        foreach ($this->additionalInvoiceItems as $item) {
            $item['invoice_id'] = $this->invoice->id;
            AdditionalInvoiceItem::query()->create($item);
        }

        // save invoice case datas
        foreach ($this->invoiceCaseDatas as $case) {
            $case->invoice_id = $this->invoice->id;
            $case->save();
        }

        // save invoice workshop datas
        foreach ($this->invoiceWorkshopDatas as $workshop) {
            $workshop->invoice_id = $this->invoice->id;
            $workshop->save();
        }

        // save invoice crisis datas
        foreach ($this->invoiceCrisisDatas as $crisis) {
            $crisis->invoice_id = $this->invoice->id;
            $crisis->save();
        }

        // save invoice other activity datas
        foreach ($this->invoiceOtherActivityDatas as $otherActivity) {
            $otherActivity->invoice_id = $this->invoice->id;
            $otherActivity->save();
        }

        // save live webinar datas
        foreach ($this->invoiceLiveWebinarDatas as $liveWebinar) {
            $liveWebinar->invoice_id = $this->invoice->id;
            $liveWebinar->save();
        }

        $grand_total_in_eur = $this->getExchangedPrice(strtoupper((string) $this->invoice->currency), 'EUR', (int) str_replace(' ', '', (string) $this->invoice->grand_total));

        if ($grand_total_in_eur >= 10_000) {
            Mail::to('anita.tompa@cgpeu.com')->send(new LargeExpertInvoiceNotification(
                Auth::user()->name,
                $grand_total_in_eur,
                $this->invoice->date_of_issue,
                $this->invoice->number
            ));
        }

        return redirect()->route('expert.invoices.main');
    }

    private function getPaymentDeadline($date): string
    {
        // if ($day < 11) {
        //     return date('Y-m-t', strtotime($date));
        // } else {
        //     return date('Y-m-t', strtotime('last day of next month', strtotime($date)));
        // }

        return date('Y-m-t', strtotime((string) $date));
    }

    private function getPrices(): array
    {
        $invoiceDatas = $this->invoiceDatas;

        $sumOf30 = $this->invoiceCaseDatas->where('duration', 30)->where('permission_id', '!=', 17)->sum('consultations_count') * (int) str_replace(' ', '', (string) $this->invoiceDatas->hourly_rate_30);
        $sumOf50 = $this->invoiceCaseDatas->whereIn('duration', [50, 60])->where('permission_id', '!=', 17)->sum('consultations_count') * (int) str_replace(' ', '', (string) $this->invoiceDatas->hourly_rate_50);
        $sumOf15 = $this->invoiceCaseDatas->where('duration', 15)->where('permission_id', '!=', 17)->sum('consultations_count') * (int) str_replace(' ', '', (string) $this->invoiceDatas->hourly_rate_15);
        $sumOfSingleSession = $this->invoiceCaseDatas->where('permission_id', 17)->sum('consultations_count') * (int) str_replace(' ', '', (string) $this->invoiceDatas->single_session_rate);

        $casesPrice = empty($this->casesPrice) ? ($sumOf50 + $sumOf30 + $sumOf15 + $sumOfSingleSession) : (int) str_replace(' ', '', (string) $this->casesPrice);

        $workshopPrice = array_sum(array_map(fn ($workshop): int => $this->getExchangedPrice($workshop['currency'], $invoiceDatas->currency, $workshop['price']), $this->invoiceWorkshopDatas->toArray()));

        $crisisPrice = array_sum(array_map(fn ($crisis): int => $this->getExchangedPrice($crisis['currency'], $invoiceDatas->currency, $crisis['price']), $this->invoiceCrisisDatas->toArray()));

        $otherActivityPrice = array_sum(array_map(fn ($otherActivity): int => $this->getExchangedPrice($otherActivity['currency'], $invoiceDatas->currency, $otherActivity['price']), $this->invoiceOtherActivityDatas->toArray()));

        $liveWebinarPrice = array_sum(array_map(fn ($liveWebinarPrice): int => $this->getExchangedPrice($liveWebinarPrice['currency'], $invoiceDatas->currency, $liveWebinarPrice['price']), $this->invoiceLiveWebinarDatas->toArray()));

        $additionalInvoiceItemsPrice = array_sum(array_map(fn ($item): int => (int) str_replace(' ', '', (string) $item['price']), $this->additionalInvoiceItems->toArray()));

        $extra_invoice_items = array_sum(array_map(fn ($item): int => (int) str_replace(' ', '', (string) $item['amount']), $this->custom_invoice_items->toArray()));

        $fixed_wage = ($this->invoiceDatas->invoicing_type === InvoicingType::TYPE_FIXED) ? (int) str_replace(' ', '', (string) $this->invoiceDatas->fixed_wage) : 0;

        if ($this->invoiceDatas->invoicing_type === InvoicingType::TYPE_CUSTOM) {
            $grand_total = number_format($additionalInvoiceItemsPrice + $extra_invoice_items + $fixed_wage, 0, ',', ' ');
        } else {
            $grand_total = number_format($casesPrice + $workshopPrice + $crisisPrice + $otherActivityPrice + $liveWebinarPrice + $additionalInvoiceItemsPrice + $extra_invoice_items + $fixed_wage, 0, ',', ' ');
        }

        return [
            'casesPrice' => number_format((float) str_replace(' ', '', $casesPrice), 0, ',', ' '),
            'workshopPrice' => number_format((float) str_replace(' ', '', $workshopPrice), 0, ',', ' '),
            'crisisPrice' => number_format((float) str_replace(' ', '', $crisisPrice), 0, ',', ' '),
            'otherActivityPrice' => number_format((float) str_replace(' ', '', $otherActivityPrice), 0, ',', ' '),
            'liveWebinarPrice' => number_format((float) str_replace(' ', '', $liveWebinarPrice), 0, ',', ' '),
            'additionalInvoiceItemsPrice' => $additionalInvoiceItemsPrice,
            'extra_invoice_items' => $extra_invoice_items,
            'grandTotal' => $grand_total,
        ];
    }

    private function getExchangedPrice($from, $to, $value): int
    {
        $currency = new CurrencyCached;
        $exchanged = $currency->convert((int) $value, strtoupper((string) $to), strtoupper((string) $from));

        return (int) round($exchanged);
    }
}
