<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ContractDateReminderEmail
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $country_id
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Country|null $country
 *
 * @method static Builder|ContractDateReminderEmail newModelQuery()
 * @method static Builder|ContractDateReminderEmail newQuery()
 * @method static Builder|ContractDateReminderEmail query()
 * @method static Builder|ContractDateReminderEmail whereCompanyId($value)
 * @method static Builder|ContractDateReminderEmail whereCountryId($value)
 * @method static Builder|ContractDateReminderEmail whereCreatedAt($value)
 * @method static Builder|ContractDateReminderEmail whereId($value)
 * @method static Builder|ContractDateReminderEmail whereUpdatedAt($value)
 * @method static Builder|ContractDateReminderEmail whereValue($value)
 *
 * @mixin \Eloquent
 */
class ContractDateReminderEmail extends Model
{
    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
