<?php

namespace App\Models;

use App\Enums\WorkshopCaseStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property int $user_id Megadja, hogy melyik felhasználóhoz tartozik
 * @property string $number
 * @property string $status
 * @property int $seen
 * @property int|null $downloaded_by
 * @property string|null $downloaded_at
 * @property string $name
 * @property string $email
 * @property string $account_number
 * @property string|null $swift
 * @property string|null $tax_number
 * @property string|null $international_tax_number
 * @property string $bank_name
 * @property string $bank_address
 * @property int $destination_country
 * @property string $currency
 * @property string|null $date_of_issue
 * @property string|null $payment_deadline
 * @property int|null $workshop_total
 * @property int|null $crisis_total
 * @property int|null $other_activity_total
 * @property int|null $cases_total
 * @property string $grand_total
 * @property string $url
 * @property string|null $deleted_by_expert_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, AdditionalInvoiceItem> $additional_invoice_items
 * @property-read int|null $additional_invoice_items_count
 * @property-read Collection<int, InvoiceCaseData> $case_datas
 * @property-read int|null $case_datas_count
 * @property-read Country $country
 * @property-read Collection<int, InvoiceCrisisData> $crisis_datas
 * @property-read int|null $crisis_datas_count
 * @property-read Collection<int, InvoiceDataChanges> $data_changes
 * @property-read int|null $data_changes_count
 * @property-read User|null $downloader
 * @property-read Collection<int, InvoiceEvent> $events
 * @property-read int|null $events_count
 * @property-read User $expert
 * @property-read Collection<int, InvoiceOtherActivityData> $other_activity_datas
 * @property-read int|null $other_activity_datas_count
 * @property-read Collection<int, InvoiceWorkshopData> $workshop_datas
 * @property-read int|null $workshop_datas_count
 *
 * @method static Builder|Invoice newModelQuery()
 * @method static Builder|Invoice newQuery()
 * @method static Builder|Invoice onlyTrashed()
 * @method static Builder|Invoice query()
 * @method static Builder|Invoice whereAccountNumber($value)
 * @method static Builder|Invoice whereBankAddress($value)
 * @method static Builder|Invoice whereBankName($value)
 * @method static Builder|Invoice whereCasesTotal($value)
 * @method static Builder|Invoice whereCreatedAt($value)
 * @method static Builder|Invoice whereCrisisTotal($value)
 * @method static Builder|Invoice whereCurrency($value)
 * @method static Builder|Invoice whereDateOfIssue($value)
 * @method static Builder|Invoice whereDeletedAt($value)
 * @method static Builder|Invoice whereDeletedByExpertAt($value)
 * @method static Builder|Invoice whereDestinationCountry($value)
 * @method static Builder|Invoice whereDownloadedAt($value)
 * @method static Builder|Invoice whereDownloadedBy($value)
 * @method static Builder|Invoice whereEmail($value)
 * @method static Builder|Invoice whereGrandTotal($value)
 * @method static Builder|Invoice whereId($value)
 * @method static Builder|Invoice whereInternationalTaxNumber($value)
 * @method static Builder|Invoice whereName($value)
 * @method static Builder|Invoice whereNumber($value)
 * @method static Builder|Invoice whereOtherActivityTotal($value)
 * @method static Builder|Invoice wherePaymentDeadline($value)
 * @method static Builder|Invoice whereSeen($value)
 * @method static Builder|Invoice whereStatus($value)
 * @method static Builder|Invoice whereSwift($value)
 * @method static Builder|Invoice whereTaxNumber($value)
 * @method static Builder|Invoice whereUpdatedAt($value)
 * @method static Builder|Invoice whereUrl($value)
 * @method static Builder|Invoice whereUserId($value)
 * @method static Builder|Invoice whereWorkshopTotal($value)
 * @method static Builder|Invoice withTrashed()
 * @method static Builder|Invoice withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoices';

    protected $guarded = [];

    protected $appends = [
        'workshop_total',
        'crisis_total',
        'other_activity_total',
        'cases_total',
        'grand_total',
    ];

    public function getWorkshopTotalAttribute()
    {
        if (empty($this->attributes['workshop_total'])) {
            return $this->attributes['workshop_total'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['workshop_total']), 0, ',', ' ');
    }

    public function setWorkshopTotalAttribute($value): void
    {
        $this->attributes['workshop_total'] = str_replace(' ', '', (string) $value);
    }

    public function getCrisisTotalAttribute()
    {
        if (empty($this->attributes['crisis_total'])) {
            return $this->attributes['crisis_total'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['crisis_total']), 0, ',', ' ');
    }

    public function setCrisisTotalAttribute($value): void
    {
        $this->attributes['crisis_total'] = str_replace(' ', '', (string) $value);
    }

    public function getOtherActivityTotalAttribute()
    {
        if (empty($this->attributes['other_activity_total'])) {
            return $this->attributes['other_activity_total'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['other_activity_total']), 0, ',', ' ');
    }

    public function setOtherActivityTotalAttribute($value): void
    {
        $this->attributes['other_activity_total'] = str_replace(' ', '', (string) $value);
    }

    public function getCasesTotalAttribute()
    {
        if (empty($this->attributes['cases_total'])) {
            return $this->attributes['cases_total'] ?? null;
        }

        return number_format((float) str_replace(' ', '', (string) $this->attributes['cases_total']), 0, ',', ' ');
    }

    public function setCasesTotalAttribute($value): void
    {
        $this->attributes['cases_total'] = str_replace(' ', '', (string) $value);
    }

    public function getGrandTotalAttribute()
    {
        if (empty($this->attributes['grand_total'])) {
            return $this->attributes['grand_total'] ?? null;
        }

        $this->attributes['grand_total'] = preg_replace('/[^0-9]/', '', (string) $this->attributes['grand_total']);

        return number_format((float) str_replace(' ', '', $this->attributes['grand_total']), 0, ',', ' ');
    }

    public function setGrandTotalAttribute($value): void
    {
        $this->attributes['grand_total'] = str_replace(' ', '', (string) $value);
    }

    public function getUrlAttribute(string $value)
    {
        return asset('/assets/docs/invoices/'.$value);
    }

    public function getDateOfIssueAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getPaymentDeadlineAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function additional_invoice_items(): HasMany
    {
        return $this->hasMany(AdditionalInvoiceItem::class);
    }

    public function case_datas(): HasMany
    {
        return $this->hasMany(InvoiceCaseData::class, 'invoice_id', 'id');
    }

    public function workshop_datas(): HasMany
    {
        return $this->hasMany(InvoiceWorkshopData::class, 'invoice_id', 'id');
    }

    public function crisis_datas(): HasMany
    {
        return $this->hasMany(InvoiceCrisisData::class, 'invoice_id', 'id');
    }

    public function other_activity_datas(): HasMany
    {
        return $this->hasMany(InvoiceOtherActivityData::class, 'invoice_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country');
    }

    public function events(): HasMany
    {
        return $this->hasMany(InvoiceEvent::class)->orderBy('invoice_events.id', 'desc');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function downloader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'downloaded_by');
    }

    public function data_changes(): HasMany
    {
        return $this->hasMany(InvoiceDataChanges::class);
    }

    public function uploadInvoice($file): void
    {
        $extension = $file->getClientOriginalExtension();

        $doc_name = 'invoice-'.time().'.'.$extension;

        $url = Auth::user()->id.'/'.$this->id.'/'.$doc_name;

        $file->storeAs('docs/invoices/'.Auth::user()->id.'/'.$this->id, $doc_name);

        $this->url = $url;

        $this->save();
    }

    private static function testCaseId($id): bool
    {
        $case = Cases::query()->where('id', $id)->first();

        if (! $case) {
            return false;
        }

        if (! $case->isMyCase()->first()) {
            return false;
        }

        return in_array($case->getRawOriginal('status'), ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed']) && $case->consultations->count() > 0;
    }

    private static function testWorkshopId($id)
    {
        $workshop_case = WorkshopCase::query()->where('activity_id', $id)->first();
        $userId = Auth::user()->id;

        // test if workshop id is exist
        if (! isset($workshop_case)) {
            return null;
        }

        // workshop need to be closed
        if ($workshop_case->status != WorkshopCaseStatus::CLOSED) {
            return null;
        }
        // workshop need to be meditated to the expert
        if ($workshop_case->expert_id != $userId) {
            return null;
        }

        return $workshop_case;
    }

    private static function testCrisisId($id)
    {
        $crisis_case = CrisisCase::query()->where('activity_id', $id)->first();
        $userId = Auth::user()->id;

        // test if crisis id is exist
        if (! isset($crisis_case)) {
            return null;
        }

        // crisis need to be closed
        if ($crisis_case->status != 3) {
            return null;
        }
        // crisis need to be meditated to the expert
        if ($crisis_case->expert_id != $userId) {
            return null;
        }

        return $crisis_case;
    }

    public static function addCaseToInvoice($caseId, $invoiceId): bool
    {
        try {
            $invoice = self::query()->findOrFail($invoiceId);
        } catch (Exception) {
            if ($invoiceId) {
                return false;
            }
        }

        if (isset($invoice) && $invoice->user_id != Auth::user()->id) {
            return false;
        }

        if (self::testWorkshopId($caseId)) {
            return true;
        }

        if (self::testCrisisId($caseId)) {
            return true;
        }

        return self::testCaseId($caseId);
    }

    public static function getList($search)
    {
        return self::query()->orderBy('id', 'desc')->with('expert', 'events', 'data_changes')->get();
    }

    public static function filter(array $params)
    {
        $invoices = self::query()->orderBy('id', 'desc');
        if ($params['invoice_number'] !== null) {
            $invoices = $invoices->where('number', $params['invoice_number']);
        }

        if ($params['expert'] !== null) {
            $invoices = $invoices->where('user_id', $params['expert']);
        }

        if ($params['invoice_name'] !== null) {
            $invoices = $invoices->where('name', $params['invoice_name']);
        }

        if ($params['date_of_issue'] !== null) {
            $invoices = $invoices->whereDate('date_of_issue', $params['date_of_issue']);
        }

        if ($params['created_at'] !== null) {
            $invoices = $invoices->whereDate('created_at', $params['created_at']);
        }

        return $invoices = $invoices->get();
    }

    /**
     * @return non-falsy-string[][]
     */
    public static function getUsersWithInvoiceNames($invoices): array
    {
        $users = [];
        foreach ($invoices as $invoice) {
            if (! isset($users[$invoice->expert->id])) {
                $users[$invoice->expert->id] = [];
            }
            if (! in_array($invoice->expert->name.'::::'.$invoice->name, $users[$invoice->expert->id])) {
                $users[$invoice->expert->id][] = $invoice->expert->name.'::::'.$invoice->name;
            }
        }
        foreach (array_keys($users) as $k) {
            sort($users[$k]);
        }
        ksort($users);

        return $users;
    }

    public function afterPaymentDeadline(): int
    {
        return Carbon::today() > Carbon::parse($this->payment_deadline) ? 1 : 0;
    }

    public function getPaidOrNotPaidEvent()
    {
        return $this->hasOne(InvoiceEvent::class)->orderBy('id', 'desc')->whereIn('event', ['invoice_paid', 'invoice_expired_and_not_paid']);
    }

    public function last_event(): ?InvoiceEvent
    {
        return $this->events->whereIn('event', ['invoice_expired_and_not_paid', 'invoice_paid', 'invoice_payment_sent'])->first();
    }
}
