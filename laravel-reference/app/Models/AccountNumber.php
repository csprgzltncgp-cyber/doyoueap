<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\AccountNumber
 *
 * @property int $id
 * @property string $account_number
 * @property string $currency
 * @property string|null $iban
 * @property int $cgp_data_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CgpData|null $cgp_data
 *
 * @method static Builder|AccountNumber newModelQuery()
 * @method static Builder|AccountNumber newQuery()
 * @method static Builder|AccountNumber query()
 * @method static Builder|AccountNumber whereAccountNumber($value)
 * @method static Builder|AccountNumber whereCgpDataId($value)
 * @method static Builder|AccountNumber whereCreatedAt($value)
 * @method static Builder|AccountNumber whereCurrency($value)
 * @method static Builder|AccountNumber whereIban($value)
 * @method static Builder|AccountNumber whereId($value)
 * @method static Builder|AccountNumber whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AccountNumber extends Model
{
    protected $guarded = [];

    public function cgp_data(): BelongsTo
    {
        return $this->belongsTo(CgpData::class);
    }
}
