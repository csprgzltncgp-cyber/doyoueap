<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\OrgData
 *
 * @property int $id
 * @property int|null $contract_holder_id
 * @property int|null $org_id
 * @property string|null $head_count
 * @property int|null $company_id
 * @property int|null $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $workshops_number
 * @property int|null $crisis_number
 * @property string|null $contract_date
 * @property string|null $contract_date_end
 * @property-read Company|null $company
 * @property-read ContractHolder|null $contract_holder
 * @property-read Country|null $country
 *
 * @method static Builder|OrgData newModelQuery()
 * @method static Builder|OrgData newQuery()
 * @method static Builder|OrgData query()
 * @method static Builder|OrgData whereCompanyId($value)
 * @method static Builder|OrgData whereContractHolder($value)
 * @method static Builder|OrgData whereContractDate($value)
 * @method static Builder|OrgData whereContractDateEnd($value)
 * @method static Builder|OrgData whereCountryId($value)
 * @method static Builder|OrgData whereCreatedAt($value)
 * @method static Builder|OrgData whereCrisisNumber($value)
 * @method static Builder|OrgData whereHeadCount($value)
 * @method static Builder|OrgData whereId($value)
 * @method static Builder|OrgData whereOrgId($value)
 * @method static Builder|OrgData whereUpdatedAt($value)
 * @method static Builder|OrgData whereWorkshopsNumber($value)
 * @method static Builder|OrgData whereContractHolderId($value)
 *
 * @mixin \Eloquent
 */
class OrgData extends Model
{
    protected $guarded = [];

    protected $table = 'org_data';

    protected $casts = [
        'contract_holder_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function contract_holder(): BelongsTo
    {
        return $this->belongsTo(ContractHolder::class, 'contract_holder_id', 'id');
    }
}
