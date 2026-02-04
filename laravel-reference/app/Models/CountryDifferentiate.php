<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CountryDifferentiate
 *
 * @property int $id
 * @property int $company_id
 * @property bool $contract_holder
 * @property bool $org_id
 * @property bool $contract_date
 * @property bool $reporting
 * @property bool $invoicing
 * @property bool $contract_date_reminder_email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 *
 * @method static Builder|CountryDifferentiate newModelQuery()
 * @method static Builder|CountryDifferentiate newQuery()
 * @method static Builder|CountryDifferentiate query()
 * @method static Builder|CountryDifferentiate whereCompanyId($value)
 * @method static Builder|CountryDifferentiate whereContractDate($value)
 * @method static Builder|CountryDifferentiate whereContractDateReminderEmail($value)
 * @method static Builder|CountryDifferentiate whereContractHolder($value)
 * @method static Builder|CountryDifferentiate whereCreatedAt($value)
 * @method static Builder|CountryDifferentiate whereId($value)
 * @method static Builder|CountryDifferentiate whereInvoicing($value)
 * @method static Builder|CountryDifferentiate whereOrgId($value)
 * @method static Builder|CountryDifferentiate whereReporting($value)
 * @method static Builder|CountryDifferentiate whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CountryDifferentiate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'contract_holder' => 'boolean',
        'org_id' => 'boolean',
        'contract_date' => 'boolean',
        'reporting' => 'boolean',
        'invoicing' => 'boolean',
        'contract_date_reminder_email' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
