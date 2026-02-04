<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\InvoiceDataChanges
 *
 * @property int $id
 * @property string $attribute
 * @property int $user_id Megadja, hogy melyik felhasználóhoz tartozik
 * @property int $invoice_id
 * @property int|null $seen_by Megadja, hogy melyik felhasználó látta a változást
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $expert
 * @property-read Invoice $invoice
 * @property-read User|null $seenBy
 *
 * @method static Builder|InvoiceDataChanges newModelQuery()
 * @method static Builder|InvoiceDataChanges newQuery()
 * @method static Builder|InvoiceDataChanges query()
 * @method static Builder|InvoiceDataChanges whereAttribute($value)
 * @method static Builder|InvoiceDataChanges whereCreatedAt($value)
 * @method static Builder|InvoiceDataChanges whereId($value)
 * @method static Builder|InvoiceDataChanges whereInvoiceId($value)
 * @method static Builder|InvoiceDataChanges whereSeenBy($value)
 * @method static Builder|InvoiceDataChanges whereUpdatedAt($value)
 * @method static Builder|InvoiceDataChanges whereUserId($value)
 *
 * @mixin \Eloquent
 */
class InvoiceDataChanges extends Model
{
    protected $table = 'invoice_data_changes';

    protected $fillable = ['attribute', 'user_id', 'invoice_id', 'seen_by'];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function seenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seen_by');
    }
}
