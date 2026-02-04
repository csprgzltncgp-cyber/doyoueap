<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CrisisIntervention
 *
 * @property int $id
 * @property string|null $activity_id
 * @property int|null $company_id
 * @property int|null $country_id
 * @property bool|null $free
 * @property string|null $contracts_date
 * @property string|null $valuta
 * @property int|null $crisis_price
 * @property int|null $contract_holder_id
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Country|null $country
 *
 * @method static Builder|CrisisIntervention newModelQuery()
 * @method static Builder|CrisisIntervention newQuery()
 * @method static Builder|CrisisIntervention query()
 * @method static Builder|CrisisIntervention whereActive($value)
 * @method static Builder|CrisisIntervention whereActivityId($value)
 * @method static Builder|CrisisIntervention whereCompanyId($value)
 * @method static Builder|CrisisIntervention whereContractHolderHelper($value)
 * @method static Builder|CrisisIntervention whereContractsDate($value)
 * @method static Builder|CrisisIntervention whereCountryId($value)
 * @method static Builder|CrisisIntervention whereCreatedAt($value)
 * @method static Builder|CrisisIntervention whereCrisisPrice($value)
 * @method static Builder|CrisisIntervention whereFree($value)
 * @method static Builder|CrisisIntervention whereId($value)
 * @method static Builder|CrisisIntervention whereUpdatedAt($value)
 * @method static Builder|CrisisIntervention whereValuta($value)
 * @method static Builder|CrisisIntervention whereContractHolderId($value)
 *
 * @mixin \Eloquent
 */
class CrisisIntervention extends Model
{
    protected $guarded = [];

    protected $casts = [
        'free' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function crisis_case(): BelongsTo
    {
        return $this->belongsTo(CrisisCase::class, 'activity_id', 'activity_id');
    }
}
