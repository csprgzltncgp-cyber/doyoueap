<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Workshop
 *
 * @property int $id
 * @property string|null $activity_id
 * @property int|null $company_id
 * @property int|null $country_id
 * @property bool $gift
 * @property bool $free
 * @property string|null $contracts_date
 * @property string|null $valuta
 * @property int|null $workshop_price
 * @property int|null $contract_holder_id
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Country|null $country
 *
 * @method static Builder|Workshop newModelQuery()
 * @method static Builder|Workshop newQuery()
 * @method static Builder|Workshop query()
 * @method static Builder|Workshop whereActive($value)
 * @method static Builder|Workshop whereActivityId($value)
 * @method static Builder|Workshop whereCompanyId($value)
 * @method static Builder|Workshop whereContractHolderHelper($value)
 * @method static Builder|Workshop whereContractsDate($value)
 * @method static Builder|Workshop whereCountryId($value)
 * @method static Builder|Workshop whereCreatedAt($value)
 * @method static Builder|Workshop whereFree($value)
 * @method static Builder|Workshop whereId($value)
 * @method static Builder|Workshop whereUpdatedAt($value)
 * @method static Builder|Workshop whereValuta($value)
 * @method static Builder|Workshop whereWorkshopPrice($value)
 *
 * @property string|null $evaluation_file
 *
 * @method static Builder|Workshop whereEvaluationFile($value)
 * @method static Builder|Workshop whereGift($value)
 * @method static Builder|Workshop whereContractHolderId($value)
 *
 * @mixin \Eloquent
 */
class Workshop extends Model
{
    protected $guarded = [];

    protected $casts = [
        'free' => 'boolean',
        'gift' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function workshop_case(): BelongsTo
    {
        return $this->belongsTo(WorkshopCase::class, 'activity_id', 'activity_id');
    }
}
