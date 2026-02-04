<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\OperatorData
 *
 * @property int $id
 * @property int $user_id
 * @property int $position
 * @property string $private_email
 * @property string|null $company_email
 * @property string $private_phone
 * @property string $company_phone
 * @property int $employment_type
 * @property string|null $start_of_employment
 * @property string $salary
 * @property string|null $salary_currency
 * @property string|null $bank_account_number
 * @property string|null $invoincing_name
 * @property string|null $invoincing_post_code
 * @property string|null $invoincing_country
 * @property string|null $invoincing_city
 * @property string|null $invoincing_street
 * @property string|null $invoincing_house_number
 * @property string|null $tax_number
 * @property string|null $language
 * @property string $eap_chat_username
 * @property string $eap_chat_password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, OperatorCompanyPhone> $company_phones
 * @property-read int|null $company_phones_count
 * @property-read Collection<int, OperatorFile> $files
 * @property-read int|null $files_count
 * @property-read User $user
 *
 * @method static Builder|OperatorData newModelQuery()
 * @method static Builder|OperatorData newQuery()
 * @method static Builder|OperatorData query()
 * @method static Builder|OperatorData whereBankAccountNumber($value)
 * @method static Builder|OperatorData whereCompanyEmail($value)
 * @method static Builder|OperatorData whereCompanyPhone($value)
 * @method static Builder|OperatorData whereCreatedAt($value)
 * @method static Builder|OperatorData whereEapChatPassword($value)
 * @method static Builder|OperatorData whereEapChatUsername($value)
 * @method static Builder|OperatorData whereEmploymentType($value)
 * @method static Builder|OperatorData whereId($value)
 * @method static Builder|OperatorData whereInvoincingCity($value)
 * @method static Builder|OperatorData whereInvoincingCountry($value)
 * @method static Builder|OperatorData whereInvoincingHouseNumber($value)
 * @method static Builder|OperatorData whereInvoincingName($value)
 * @method static Builder|OperatorData whereInvoincingPostCode($value)
 * @method static Builder|OperatorData whereInvoincingStreet($value)
 * @method static Builder|OperatorData whereLanguage($value)
 * @method static Builder|OperatorData wherePosition($value)
 * @method static Builder|OperatorData wherePrivateEmail($value)
 * @method static Builder|OperatorData wherePrivatePhone($value)
 * @method static Builder|OperatorData whereSalary($value)
 * @method static Builder|OperatorData whereSalaryCurrency($value)
 * @method static Builder|OperatorData whereStartOfEmployment($value)
 * @method static Builder|OperatorData whereTaxNumber($value)
 * @method static Builder|OperatorData whereUpdatedAt($value)
 * @method static Builder|OperatorData whereUserId($value)
 *
 * @mixin \Eloquent
 */
class OperatorData extends Model
{
    final public const POSITION_DAY = 1;

    final public const POSITION_NIGHT = 2;

    final public const POSITION_DAY_NIGHT = 3;

    final public const EMPLOYMENT_TYPE_FULL_TIME = 1;

    final public const EMPLOYMENT_TYPE_PART_TIME = 2;

    final public const EMPLOYMENT_TYPE_CASUAL_EMPLOYMENT = 3;

    final public const EMPLOYMENT_TYPE_CONTRACT = 4;

    protected $guarded = [];

    protected $table = 'operator_datas';

    protected $casts = [
        'position' => 'integer',
        'salary' => 'integer',
        'employment_type' => 'integer',
    ];

    public function setPositionAttribute($value): void
    {
        $this->attributes['position'] = (int) $value;
    }

    public function setEmploymentTypeAttribute($value): void
    {
        $this->attributes['employment_type'] = (int) $value;
    }

    public function getSalaryAttribute($value): string
    {
        return number_format($value, 0, ',', '.');
    }

    public function setSalaryAttribute($value): void
    {
        $this->attributes['salary'] = (int) str_replace('.', '', (string) $value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company_phones(): HasMany
    {
        return $this->hasMany(OperatorCompanyPhone::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(OperatorFile::class);
    }
}
