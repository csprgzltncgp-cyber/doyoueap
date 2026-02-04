<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\CgpData
 *
 * @property int $id
 * @property string $company_name
 * @property string $country
 * @property string $post_code
 * @property string $city
 * @property string $street
 * @property string $house_number
 * @property string $vat_number
 * @property string $eu_vat_number
 * @property string $iban
 * @property string $swift
 * @property string $email
 * @property string $website
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AccountNumber> $account_numbers
 * @property-read int|null $account_numbers_count
 *
 * @method static Builder|CgpData newModelQuery()
 * @method static Builder|CgpData newQuery()
 * @method static Builder|CgpData query()
 * @method static Builder|CgpData whereCity($value)
 * @method static Builder|CgpData whereCompanyName($value)
 * @method static Builder|CgpData whereCountry($value)
 * @method static Builder|CgpData whereCreatedAt($value)
 * @method static Builder|CgpData whereEmail($value)
 * @method static Builder|CgpData whereEuVatNumber($value)
 * @method static Builder|CgpData whereHouseNumber($value)
 * @method static Builder|CgpData whereIban($value)
 * @method static Builder|CgpData whereId($value)
 * @method static Builder|CgpData wherePostCode($value)
 * @method static Builder|CgpData whereStreet($value)
 * @method static Builder|CgpData whereSwift($value)
 * @method static Builder|CgpData whereUpdatedAt($value)
 * @method static Builder|CgpData whereVatNumber($value)
 * @method static Builder|CgpData whereWebsite($value)
 *
 * @mixin \Eloquent
 */
class CgpData extends Model
{
    protected $guarded = [];

    public function account_numbers(): HasMany
    {
        return $this->hasMany(AccountNumber::class);
    }
}
