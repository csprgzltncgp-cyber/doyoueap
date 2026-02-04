<?php

namespace App\Http\Livewire\Admin\InvoiceHelper\DirectInvoicing;

use App\Enums\ContractHolderCompany;
use App\Mail\DirectInvoiceEmail;
use App\Models\CgpData;
use App\Models\DirectBillingData;
use App\Models\DirectBillingDataEmail;
use App\Models\DirectInvoice;
use App\Models\OtherActivity;
use App\Models\WorkshopCase;
use App\Traits\InvoiceHelper\EnvelopeTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use ZipArchive;
use zoparga\SzamlazzHu\Client\Client;
use zoparga\SzamlazzHu\Internal\Support\PaymentMethods;
use zoparga\SzamlazzHu\Invoice;

class Card extends Component
{
    use EnvelopeTrait;

    public $direct_invoice_data;

    public DirectInvoice $direct_invoice;

    public $date_of_invoice;

    public $date_of_completion;

    public $date_of_payment;

    public $paymentMethod = 'transfer';

    protected $rules = [
        'direct_invoice_data.invoice_data.po_number' => 'nullable|string',
        'direct_invoice_data.billing_data.custom_email_subject' => 'nullable|string',
        'direct_invoice_data.invoice_items.*.amount.value' => 'nullable|numeric',
        'direct_invoice_data.invoice_items.*.volume.value' => 'nullable|numeric',
    ];

    protected $listeners = [
        'cancelInvoice' => 'cancel_invoice',
    ];

    public function mount($direct_invoice_id): void
    {
        $this->direct_invoice = DirectInvoice::query()->where('id', $direct_invoice_id)->first();

        $this->direct_invoice_data = $this->direct_invoice->data;

        // Remove any space from the volume values
        $this->direct_invoice_data['invoice_items'] = collect($this->direct_invoice_data['invoice_items'])->map(function (array $item): array {
            if (isset($item['volume']['value'])) {
                $item['volume']['value'] = Str::replace(' ', '', $item['volume']['value']);
            }
            if (isset($item['amount']['value'])) {
                $item['amount']['value'] = Str::replace(' ', '', $item['amount']['value']);
            }

            return $item;
        });
    }

    public function render()
    {
        $this->calclate_dates();

        $net_total = get_invoice_net_total($this->direct_invoice_data);
        $total = get_invoice_net_total($this->direct_invoice_data) + (get_invoice_net_total($this->direct_invoice_data) * (int) $this->direct_invoice_data['billing_data']['vat_rate'] / 100);

        if ((data_get($this->direct_invoice_data['billing_data'], 'inside_eu') && $this->direct_invoice_data['billing_data']['inside_eu']) || (data_get($this->direct_invoice_data['billing_data'], 'outside_eu') && $this->direct_invoice_data['billing_data']['outside_eu'])) {
            $tax = ($this->direct_invoice_data['billing_data']['inside_eu']) ? 'EUFAD37' : 'HO';
        } else {
            $tax = ($this->direct_invoice->data['billing_data']['tehk']) ? 'TEHK' : $this->direct_invoice->data['billing_data']['vat_rate'].'%';
        }

        return view('livewire.admin.invoice-helper.direct-invoicing.card', ['net_total' => $net_total, 'total' => $total, 'tax' => $tax]);
    }

    public function updatedDirectInvoiceData(): void
    {
        $this->validate();

        $this->direct_invoice->data = $this->direct_invoice_data;
        $this->direct_invoice->save();
    }

    public function generate_invoice()
    {
        if (! empty($this->direct_invoice->invoice_number)) {
            return null;
        }

        $this->calclate_dates();

        $invoice = new Invoice;
        $invoice->isElectronic = false;
        $invoice->invoiceLanguage = strtolower($this->direct_invoice->data['billing_data']['invoice_language'] ?? 'en');
        $invoice->currency = strtoupper((string) $this->direct_invoice->data['billing_data']['currency']);
        $invoice->fulfillmentAt = Carbon::parse($this->date_of_completion);
        $invoice->paymentDeadline = Carbon::parse($this->date_of_payment);
        $invoice->paymentMethod = PaymentMethods::$paymentMethods[$this->paymentMethod];
        $invoice->isImprestInvoice = false;
        $invoice->isFinalInvoice = false;
        $invoice->exchangeRateBank = 'MNB';

        if ($this->direct_invoice->data['invoice_data']['po_number']) {
            $invoice->comment = trans('invoice-helper.order-number', [], strtolower($this->direct_invoice->data['billing_data']['invoice_language'] ?? 'en')).': '.$this->direct_invoice->data['invoice_data']['po_number'];
        }

        if (! empty($this->direct_invoice->data['invoice_notes'])) {
            foreach ($this->direct_invoice->data['invoice_notes'] as $invoice_note) {
                $invoice->comment .= ($invoice->comment !== '') ? "\n".$invoice_note['value'] : $invoice_note['value'];
            }
        }

        // Add tax number to comment if both group id and tax number is set.
        if ($this->direct_invoice->data['invoice_data']['tax_number'] != '' && $this->direct_invoice->data['invoice_data']['group_id'] != '') {
            $space = ($invoice->comment == '') ? '' : ', ';
            $note = $space.trans('common.tax_number', [], $invoice->invoiceLanguage).': '.$this->direct_invoice->data['invoice_data']['tax_number'];
            $invoice->comment .= $note;
        }

        if (! empty($this->direct_invoice->data['invoice_data']['group_id'])) {
            $cutomer_tax_number = $this->direct_invoice->data['invoice_data']['group_id'];
        } elseif (! empty($this->direct_invoice->data['invoice_data']['tax_number'])) {
            $cutomer_tax_number = $this->direct_invoice->data['invoice_data']['tax_number'];
        } else {
            $cutomer_tax_number = $this->direct_invoice->data['invoice_data']['community_tax_number'];
        }

        $customerData = [
            'name' => $this->direct_invoice->data['invoice_data']['name'],
            'zipCode' => $this->direct_invoice->data['invoice_data']['postal_code'],
            'city' => $this->direct_invoice->data['invoice_data']['city'],
            'address' => ucwords((string) $this->direct_invoice->data['invoice_data']['street']).' '.$this->direct_invoice->data['invoice_data']['house_number'],
        ];

        if ($this->direct_invoice->data['billing_data']['inside_eu']) {
            $customerData['taxNumberEU'] = $cutomer_tax_number;
            $customerData['taxSubject'] = Client::EU_COMPANY;
        } else {
            $customerData['taxNumber'] = $cutomer_tax_number;
            $customerData['taxSubject'] = ($this->direct_invoice->data['billing_data']['outside_eu']) ? Client::NON_EU_COMPANY : Client::HUNGARIAN_TAX_ID;
        }

        $invoice->setCustomer($customerData);

        $cgp_data = CgpData::query()->with('account_numbers')->first();

        $bank_account_number = $cgp_data->account_numbers->where('currency', strtolower((string) $this->direct_invoice->data['billing_data']['currency']))->first() ? $cgp_data->account_numbers->where('currency', strtolower((string) $this->direct_invoice->data['billing_data']['currency']))->first()->account_number : $cgp_data->account_numbers->where('currency', strtolower('usd'))->first()->account_number;
        $iban = $cgp_data->account_numbers->where('currency', strtolower((string) $this->direct_invoice->data['billing_data']['currency']))->first() ? $cgp_data->account_numbers->where('currency', strtolower((string) $this->direct_invoice->data['billing_data']['currency']))->first()->iban : $cgp_data->account_numbers->where('currency', strtolower('usd'))->first()->iban;
        $swift = $cgp_data->swift;

        $invoice->setMerchant([
            'bank' => config('szamlazz-hu.merchant.bank_name').',SWIFT:'.$swift,
            'bankAccountNumber' => $bank_account_number.', IBAN:'.$iban,
        ]);

        // Set tax rate
        if ((data_get($this->direct_invoice_data['billing_data'], 'inside_eu') && $this->direct_invoice_data['billing_data']['inside_eu']) || (data_get($this->direct_invoice_data['billing_data'], 'outside_eu') && $this->direct_invoice_data['billing_data']['outside_eu'])) {
            $taxRate = ($this->direct_invoice->data['billing_data']['inside_eu']) ? 'EUFAD37' : 'HO';
        } else {
            $taxRate = (int) $this->direct_invoice->data['billing_data']['vat_rate'];
        }

        foreach ($this->direct_invoice->data['invoice_items'] as $invoice_item) {
            if (array_key_exists('with_timestamp', $invoice_item) && $invoice_item['with_timestamp']) {
                Carbon::setLocale(strtolower($this->direct_invoice->data['billing_data']['invoice_language'] ?? 'en'));
                switch ($this->direct_invoice->data['billing_data']['billing_frequency']) {
                    case DirectBillingData::FREQUENCY_QUARTELY:
                        $months = collect([]);
                        $months->push(Carbon::parse($this->direct_invoice->to)->subMonthNoOverflow()->subMonthNoOverflow()->translatedFormat('F'));
                        $months->push(Carbon::parse($this->direct_invoice->to)->subMonthNoOverflow()->translatedFormat('F'));
                        $months->push(Carbon::parse($this->direct_invoice->to)->translatedFormat('F'));
                        break;
                    case DirectBillingData::FREQUENCY_YEARLY:
                        $months = collect([]);
                        break;
                    default:
                        $months = collect([]);
                        $months->push(Carbon::parse($this->direct_invoice->to)->translatedFormat('F'));
                        break;
                }

                $invoice_item['name'] = $invoice_item['name'].' - '.Carbon::parse($this->direct_invoice->to)->format('Y').' '.$months->implode(',');
                Carbon::setLocale(config('app.locale'));
            }

            if (! empty($invoice_item['amount']) && ! empty($invoice_item['volume'])) {
                $quantity = $invoice_item['shown_by_item'] ? (int) str_replace(' ', '', (string) $invoice_item['volume']['value']) : 1;
                $netUnitPrice = $invoice_item['shown_by_item'] ? (float) str_replace(' ', '', (string) $invoice_item['amount']['value']) : (float) str_replace(' ', '', (string) $invoice_item['amount']['value']) * (int) str_replace(' ', '', (string) $invoice_item['volume']['value']);

                $invoice->addItem([
                    'name' => $invoice_item['name'],
                    'quantity' => $quantity,
                    'quantityUnit' => 'db',
                    'netUnitPrice' => $netUnitPrice,
                    'taxRate' => $taxRate,
                    'comment' => empty($invoice_item['comment']) ? '' : $invoice_item['comment'],
                ]);
            } elseif (! empty($invoice_item['amount']) && empty($invoice_item['volume'])) {
                $invoice->addItem([
                    'name' => $invoice_item['name'],
                    'quantity' => 1,
                    'quantityUnit' => 'db',
                    'netUnitPrice' => (float) str_replace(' ', '', (string) $invoice_item['amount']['value']),
                    'taxRate' => $taxRate,
                    'comment' => empty($invoice_item['comment']) ? '' : $invoice_item['comment'],
                ]);
            }
        }

        if (array_key_exists('workshop_datas', $this->direct_invoice->data) && ! empty($this->direct_invoice->data['workshop_datas'])) {
            foreach ($this->direct_invoice->data['workshop_datas'] as $workshop_data) {
                $workshop_case = WorkshopCase::query()->where('activity_id',
                    (Str::contains($workshop_data['activity_id'], '#')) ? // IF activity_id contains '#' then remove it.
                    substr((string) $workshop_data['activity_id'], 1) :
                    $workshop_data['activity_id']
                )->first();

                $invoice->addItem([
                    'name' => $workshop_data['activity_id'].' - '.ucfirst((string) $workshop_case->topic),
                    'quantity' => 1,
                    'quantityUnit' => 'db',
                    'netUnitPrice' => (float) str_replace(' ', '', (string) $workshop_data['price']),
                    'taxRate' => $taxRate,
                    'comment' => empty($invoice_item['comment']) ? '' : $invoice_item['comment'],
                ]);
            }
        }

        if (array_key_exists('crisis_datas', $this->direct_invoice->data) && ! empty($this->direct_invoice->data['crisis_datas'])) {
            foreach ($this->direct_invoice->data['crisis_datas'] as $crisis_data) {
                if (empty($crisis_data['activity_id'])) {
                    continue;
                }

                $invoice->addItem([
                    'name' => $crisis_data['activity_id'],
                    'quantity' => 1,
                    'quantityUnit' => 'db',
                    'netUnitPrice' => (float) str_replace(' ', '', (string) $crisis_data['price']),
                    'taxRate' => $taxRate,
                    'comment' => empty($invoice_item['comment']) ? '' : $invoice_item['comment'],
                ]);
            }
        }

        if (array_key_exists('other_activity_datas', $this->direct_invoice->data) && ! empty($this->direct_invoice->data['other_activity_datas'])) {
            foreach ($this->direct_invoice->data['other_activity_datas'] as $other_activity_data) {
                if (! array_key_exists('activity_id', $other_activity_data)) {
                    continue;
                }

                $other_activity = OtherActivity::query()->where('activity_id', $other_activity_data['activity_id'])->first();

                if ($this->direct_invoice->company_id === ContractHolderCompany::LIFEWORKS->value) {
                    $item_name = 'orientation - '.$other_activity->company->name.' - '.Carbon::parse($other_activity->date)->format('Y-m-d');
                } else {
                    $item_name = $other_activity_data['activity_id'];
                }

                $invoice->addItem([
                    'name' => $item_name,
                    'quantity' => 1,
                    'quantityUnit' => 'db',
                    'netUnitPrice' => (float) str_replace(' ', '', (string) $other_activity_data['price']),
                    'taxRate' => $taxRate,
                    'comment' => empty($invoice_item['comment']) ? '' : $invoice_item['comment'],
                ]);
            }
        }

        try {
            $invoice->save();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.invoice-generation-error')]);
        }

        $this->direct_invoice->update([
            'invoice_number' => $invoice->invoiceNumber,
        ]);

        $this->emit('alert', ['type' => 'success', 'message' => __('invoice-helper.invoice-generated')]);

        return null;
    }

    public function cancel_invoice()
    {
        if (empty($this->direct_invoice->invoice_number)) {
            return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.missing-invoice-error')]);
        }

        $client = app(Client::class);

        try {
            /** @var Invoice $invoice */
            $invoice = $client->getInvoice($this->direct_invoice->invoice_number);
            $invoice->createdAt = Carbon::now();
            $invoice->cancel();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.invoice-cancelled-error')]);
        }

        $zip = new ZipArchive;
        $zipName = 'szamlazzhu/'.$this->direct_invoice->invoice_number.'.zip';
        $zip->open(storage_path('app/'.$zipName), ZipArchive::CREATE);
        $zip->addFile(storage_path('app/szamlazzhu/'.$this->direct_invoice->invoice_number.'.pdf'), $this->direct_invoice->invoice_number.'.pdf');
        $zip->addFile(storage_path('app/szamlazzhu/'.$invoice->getCancellationInvoice()->invoiceNumber.'.pdf'), $invoice->getCancellationInvoice()->invoiceNumber.'.pdf');
        $zip->close();

        $this->direct_invoice->invoice_number = null;
        $this->direct_invoice->downloaded_at = null;
        $this->direct_invoice->sent_at = null;
        $this->direct_invoice->invoice_uploaded_at = null;
        $this->direct_invoice->paid_at = null;

        $this->direct_invoice->load(['envelope', 'completion_certificate']);

        if (! empty($this->direct_invoice->envelope)) {
            $this->direct_invoice->envelope->update([
                'printed_at' => null,
            ]);
        }

        if (! empty($this->direct_invoice->completion_certificate)) {
            $this->direct_invoice->completion_certificate->update([
                'printed_at' => null,
                'sent_at' => null,
                'uploaded_at' => null,
            ]);
        }

        $this->direct_invoice->save();

        return Storage::disk('private')->download($zipName);
    }

    public function download_invoice()
    {
        try {
            if (empty($this->direct_invoice->invoice_number)) {
                return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.missing-invoice-error')]);
            }

            $this->direct_invoice->update([
                'downloaded_at' => Carbon::now(),
            ]);

            return Storage::disk('private')->download('szamlazzhu/'.$this->direct_invoice->invoice_number.'.pdf', 'invoice-'.$this->direct_invoice->invoice_number.'.pdf');
        } catch (Exception) {
            return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.missing-invoice-error')]);
        }
    }

    public function show_invoice(): void
    {
        $file = Storage::disk('private')->get('szamlazzhu/'.$this->direct_invoice->invoice_number.'.pdf');

        $this->emit('show-pdf', ['file' => base64_encode($file), 'name' => $this->direct_invoice->invoice_number.'.pdf']);
    }

    public function send_email()
    {
        $attachments = [];

        if (empty($this->direct_invoice->invoice_number)) {
            return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.missing-invoice-error')]);
        }

        if ($this->direct_invoice->data['billing_data']['send_invoice_by_email'] && $this->direct_invoice->data['billing_data']['send_completion_certificate_by_email'] && (! empty($this->direct_invoice->completion_certificate->sent_at) && ! empty($this->direct_invoice->sent_at))) {
            return null;
        }

        if ($this->direct_invoice->data['billing_data']['send_invoice_by_email'] && ! $this->direct_invoice->data['billing_data']['send_completion_certificate_by_email'] && ! empty($this->direct_invoice->sent_at)) {
            return null;
        }

        if (! $this->direct_invoice->data['billing_data']['send_invoice_by_email'] && $this->direct_invoice->data['billing_data']['send_completion_certificate_by_email'] && ! empty($this->direct_invoice->completion_certificate->sent_at)) {
            return null;
        }

        if ($this->direct_invoice->data['billing_data']['send_invoice_by_email']) {
            if (! Storage::disk('private')->exists('szamlazzhu/'.$this->direct_invoice->invoice_number.'.pdf')) {
                return $this->emit('alert', ['type' => 'error', 'message' => 'Missing invoice file']);
            }

            $attachments[] = Attachment::fromStorageDisk(
                'private',
                'szamlazzhu/'.$this->direct_invoice->invoice_number.'.pdf'
            )->as('invoice.pdf')
                ->withMime('application/pdf');

            $this->direct_invoice->sent_at = now();
            $this->direct_invoice->save();
        }

        if ($this->direct_invoice->data['billing_data']['send_completion_certificate_by_email']) {
            $completion_certificate = $this->direct_invoice->completion_certificate;

            if (empty($completion_certificate->filename) || ! Storage::disk('private')->exists('completion-certificates/'.$completion_certificate->filename)) {
                $filename = uniqid('completion_certificate_').'.pdf';

                Pdf::loadView(
                    'admin.invoice-helper.completion-certificate.document',
                    [
                        'with_header' => true,
                        'company' => $this->direct_invoice->company,
                        'direct_invoice' => $this->direct_invoice,
                        'language' => strtolower($this->direct_invoice->data['billing_data']['invoice_language'] ?? 'en'),
                    ],
                    [],
                    'UTF-8'
                )->setPaper('a4', 'portrait')->save('completion-certificates/'.$filename, 'private');

                $completion_certificate->filename = $filename;
            } else {
                $filename = $completion_certificate->filename;
            }

            if (! Storage::disk('private')->exists('completion-certificates/'.$filename)) {
                return $this->emit('alert', ['type' => 'error', 'message' => 'Missing completion certificate!']);
            }

            $attachments[] = Attachment::fromStorageDisk(
                'private',
                'completion-certificates/'.$filename,
            )->as('completion_certificate.pdf')
                ->withMime('application/pdf');

            $completion_certificate->sent_at = now();
            $completion_certificate->save();
        }

        $emails = DirectBillingDataEmail::query()->where('direct_billing_data_id', $this->direct_invoice->data['billing_data']['id'])->get();

        if ($emails->count() <= 0) {
            return $this->emit('alert', ['type' => 'success', 'message' => __('invoice-helper.send-email-error')]);
        }

        $not_cc_emails = $emails->filter(fn ($email): bool => $email->is_cc == false)->pluck('email')->toArray();

        $cc_emails = $emails->filter(fn ($email): bool => $email->is_cc == true)->pluck('email')->toArray();

        $cc_emails[] = 'finance@cgpeu.com';

        $custom_subject = array_key_exists('custom_email_subject', $this->direct_invoice->data['billing_data']) && ! empty($this->direct_invoice->data['billing_data']['custom_email_subject']) ? $this->direct_invoice->data['billing_data']['custom_email_subject'] : 'Invoice';
        $language = strtolower($this->direct_invoice->data['billing_data']['invoice_language'] ?? 'en');

        try {
            Mail::to($not_cc_emails)->cc($cc_emails)->send(
                new DirectInvoiceEmail(
                    $attachments,
                    $language,
                    $custom_subject
                )
            );
        } catch (Exception $e) {
            $this->direct_invoice->sent_at = null;
            $this->direct_invoice->save();

            if ($this->direct_invoice->data['billing_data']['send_completion_certificate_by_email']) {
                $completion_certificate = $this->direct_invoice->completion_certificate;

                $completion_certificate->sent_at = null;
                $completion_certificate->save();
            }

            Log::error('Failed to send email: '.$e->getMessage());

            return $this->emit('alert', ['type' => 'error', 'message' => __('invoice-helper.send-email-error')]);
        }

        return $this->emit('alert', ['type' => 'success', 'message' => __('invoice-helper.send-email-success')]);
    }

    public function upload_invoice(): void
    {
        $this->direct_invoice->invoice_uploaded_at = now();
        $this->direct_invoice->save();
    }

    public function update_invoice_paid(string $paid_at, string $paid_amount): void
    {
        $this->direct_invoice->paid_amount = trim($paid_amount) === '' ? null : (float) trim($paid_amount);
        $this->direct_invoice->paid_at = trim($paid_at) === '' ? null : Carbon::parse(trim($paid_at));

        $this->direct_invoice->save();
    }

    public function upload_completion_certificate(): void
    {
        $this->direct_invoice->completion_certificate->uploaded_at = now();
        $this->direct_invoice->completion_certificate->save();
    }

    private function calclate_dates(): void
    {
        $this->date_of_invoice = Carbon::now();
        $this->date_of_payment = Carbon::now()->addDays((int) $this->direct_invoice_data['invoice_data']['payment_deadline']);
        $test = Carbon::parse($this->direct_invoice->to)->endOfMonth()->addDays(60);

        $this->date_of_completion = $test->gt($this->date_of_payment) ? $this->date_of_payment : $test;

        $this->date_of_invoice = $this->date_of_invoice->format('Y-m-d');
        $this->date_of_payment = $this->date_of_payment->format('Y-m-d');
        $this->date_of_completion = $this->date_of_completion->format('Y-m-d');
    }
}
