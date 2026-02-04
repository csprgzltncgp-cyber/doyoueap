<?php

namespace App\Models;

use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapUser;
use App\Scopes\CountryScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $code Pl. hu, en, de, it
 * @property string $timezone
 * @property string|null $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $name
 * @property-read Collection<int, City> $cities
 * @property-read int|null $cities_count
 * @property-read Collection<int, Company> $companies
 * @property-read int|null $companies_count
 * @property-read Collection<int, CustomerSatisfaction> $customerSatisfactions
 * @property-read int|null $customer_satisfactions_count
 * @property-read Collection<int, DirectBillingData> $direct_billing_datas
 * @property-read int|null $direct_billing_datas_count
 * @property-read Collection<int, DirectInvoiceData> $direct_invoice_datas
 * @property-read int|null $direct_invoice_datas_count
 * @property-read Collection<int, EapLanguage> $eap_languages
 * @property-read int|null $eap_languages_count
 * @property-read Collection<int, EapUser> $eap_users
 * @property-read int|null $eap_users_count
 * @property-read Collection<int, User> $experts
 * @property-read int|null $experts_count
 * @property-read Collection<int, InvoiceComment> $invoice_comments
 * @property-read int|null $invoice_comments_count
 * @property-read Collection<int, InvoiceItem> $invoice_items
 * @property-read int|null $invoice_items_count
 * @property-read Collection<int, InvoiceNote> $invoice_notes
 * @property-read int|null $invoice_notes_count
 *
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country onlyTrashed()
 * @method static Builder|Country query()
 * @method static Builder|Country whereCode($value)
 * @method static Builder|Country whereCreatedAt($value)
 * @method static Builder|Country whereDeletedAt($value)
 * @method static Builder|Country whereEmail($value)
 * @method static Builder|Country whereId($value)
 * @method static Builder|Country whereName($value)
 * @method static Builder|Country whereTimezone($value)
 * @method static Builder|Country whereUpdatedAt($value)
 * @method static Builder|Country withTrashed()
 * @method static Builder|Country withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CountryScope);
    }

    public function getCodeAttribute($value)
    {
        return $this->name;
    }

    public function direct_billing_datas(): HasMany
    {
        return $this->hasMany(DirectBillingData::class);
    }

    public function direct_invoice_datas(): HasMany
    {
        return $this->hasMany(DirectInvoiceData::class);
    }

    public function invoice_items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function invoice_comments(): HasMany
    {
        return $this->hasMany(InvoiceComment::class);
    }

    public function invoice_notes(): HasMany
    {
        return $this->hasMany(InvoiceNote::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function experts(): HasMany
    {
        return $this->hasMany(User::class)->where('type', 'expert');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_x_country');
    }

    public function customerSatisfactions(): HasMany
    {
        return $this->hasMany(CustomerSatisfaction::class);
    }

    public function eap_users(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapUser::class);
    }

    public function eap_languages(): BelongsToMany
    {
        $dbname = DB::connection('mysql_eap_online')->getDatabaseName();

        return $this->setConnection('mysql_eap_online')->belongsToMany(EapLanguage::class, $dbname.'.country_language', 'country_id', 'language_id');
    }
}
