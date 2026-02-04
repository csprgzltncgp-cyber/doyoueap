<?php

namespace App\Models;

use App\Enums\ConsultationType;
use App\Models\EapOnline\EapContactInformation;
use App\Models\EapOnline\EapRiport;
use App\Models\EapOnline\EapUser;
use App\Models\EapOnline\Statistics\EapAssessment;
use App\Models\EapOnline\Statistics\EapCategory;
use App\Models\EapOnline\Statistics\EapLogin;
use App\Models\EapOnline\Statistics\EapSelfHelp;
use App\Models\Scopes\ContractHolderCompanyScope;
use App\Scopes\CountryScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Company
 *
 * @property int $id
 * @property string $name Cég neve
 * @property string|null $orgId
 * @property int $customer_satisfaction_index Van-e jogosultsága megtekinteni az elégedettségi indexeket
 * @property int $eap_online_riport
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, CaseInput> $caseInputs
 * @property-read int|null $case_inputs_count
 * @property-read Collection<int, Cases> $cases
 * @property-read int|null $cases_count
 * @property-read Collection<int, User> $clientUser
 * @property-read int|null $client_user_count
 * @property-read Collection<int, User> $clientUsers
 * @property-read int|null $client_users_count
 * @property-read Collection<int, ContractDateReminderEmail> $contract_date_reminder_emails
 * @property-read int|null $contract_date_reminder_emails_count
 * @property-read Collection<int, Country> $countries
 * @property-read int|null $countries_count
 * @property-read CountryDifferentiate|null $country_differentiates
 * @property-read Collection<int, CustomerSatisfaction> $customer_satisfactions
 * @property-read int|null $customer_satisfactions_count
 * @property-read Collection<int, DirectBillingData> $direct_billing_datas
 * @property-read int|null $direct_billing_datas_count
 * @property-read DirectInvoice|null $direct_invoice
 * @property-read Collection<int, DirectInvoiceData> $direct_invoice_datas
 * @property-read int|null $direct_invoice_datas_count
 * @property-read Collection<int, DirectInvoice> $direct_invoices
 * @property-read int|null $direct_invoices_count
 * @property-read Collection<int, EapAssessment> $eap_assessment_statistics
 * @property-read int|null $eap_assessment_statistics_count
 * @property-read Collection<int, EapCategory> $eap_category_statistics
 * @property-read int|null $eap_category_statistics_count
 * @property-read EapContactInformation|null $eap_contact_information
 * @property-read Collection<int, EapLogin> $eap_login_statistics
 * @property-read int|null $eap_login_statistics_count
 * @property-read Collection<int, EapRiport> $eap_riports
 * @property-read int|null $eap_riports_count
 * @property-read Collection<int, EapSelfHelp> $eap_self_help_statistics
 * @property-read int|null $eap_self_help_statistics_count
 * @property-read Collection<int, EapUser> $eap_users
 * @property-read int|null $eap_users_count
 * @property-read Collection<int, InvoiceComment> $invoice_comments
 * @property-read int|null $invoice_comments_count
 * @property-read Collection<int, InvoiceItem> $invoice_items
 * @property-read int|null $invoice_items_count
 * @property-read Collection<int, InvoiceNote> $invoice_notes
 * @property-read int|null $invoice_notes_count
 * @property-read Collection<int, Language> $languages
 * @property-read int|null $languages_count
 * @property-read Collection<int, OrgData> $org_datas
 * @property-read int|null $org_datas_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Riport> $riports
 * @property-read int|null $riports_count
 * @property-read Collection<int, CaseInput> $steps
 * @property-read int|null $steps_count
 *
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company onlyTrashed()
 * @method static Builder|Company query()
 * @method static Builder|Company whereActive($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereCustomerSatisfactionIndex($value)
 * @method static Builder|Company whereDeletedAt($value)
 * @method static Builder|Company whereEapOnlineRiport($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereOrgId($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company withTrashed()
 * @method static Builder|Company withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CountryScope);
        static::addGlobalScope(new ContractHolderCompanyScope);
        static::deleting(function ($company): void {
            $company->activity_plans->map->delete();
        });
    }

    public function direct_invoices(): HasMany
    {
        return $this->hasMany(DirectInvoice::class);
    }

    public function direct_invoice(): HasOne
    {
        return $this->hasOne(DirectInvoice::class);
    }

    public function direct_billing_datas(): HasMany
    {
        return $this->hasMany(DirectBillingData::class);
    }

    public function direct_invoice_datas(): HasMany
    {
        return $this->hasMany(DirectInvoiceData::class);
    }

    public function contract_date_reminder_emails(): HasMany
    {
        return $this->hasMany(ContractDateReminderEmail::class);
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

    public function country_differentiates(): HasOne
    {
        return $this->hasOne(CountryDifferentiate::class);
    }

    public function org_datas(): HasMany
    {
        return $this->hasMany(OrgData::class, 'company_id');
    }

    public function eap_users(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapUser::class);
    }

    public function eap_riports(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapRiport::class);
    }

    public function eap_self_help_statistics(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapSelfHelp::class);
    }

    public function eap_login_statistics(): HasManyThrough
    {
        return $this->setConnection('mysql_eap_online')->hasManyThrough(EapLogin::class, EapUser::class, 'company_id', 'user_id', 'id', 'id');
    }

    public function eap_category_statistics(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapCategory::class);
    }

    public function eap_assessment_statistics(): HasMany
    {
        return $this->setConnection('mysql_eap_online')->hasMany(EapAssessment::class);
    }

    public function eap_contact_information(): HasOne
    {
        return $this->setConnection('mysql_eap_online')->hasOne(EapContactInformation::class, 'company_id', 'id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_x_company')->withPivot(['number', 'duration', 'contact']);
    }

    public function caseInputs(): HasMany
    {
        return $this->hasMany(CaseInput::class)->orWhere('company_id', null)->orderBy('created_at')->select('id', 'default_type', 'type', 'company_id', 'name', 'delete_later');
    }

    public function countries(): BelongsToMany
    {
        return $this->setConnection('mysql')->belongsToMany(Country::class, 'company_x_country');
    }

    public function clientUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_x_company');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'company_x_language');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(Cases::class);
    }

    public function riports(): HasMany
    {
        return $this->hasMany(Riport::class);
    }

    public function customer_satisfactions(): HasMany
    {
        return $this->hasMany(CustomerSatisfaction::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(CaseInput::class);
    }

    public function clientUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_x_company');
    }

    public function workshop_cases(): HasMany
    {
        return $this->hasMany(WorkshopCase::class);
    }

    public function crisis_cases(): HasMany
    {
        return $this->hasMany(CrisisCase::class);
    }

    public function usedPermissions()
    {
        $case_ids = $this->cases->pluck('id');

        return Consultation::query()->whereIn('id', $case_ids)->groupBy('permission_id')->select('permission_id', DB::raw('count(id) as db'))->get();
    }

    protected function edit($id, array $data, $countries, $languages)
    {
        self::query()->where('id', $id)->update($data);
        $company = self::query()->findOrFail($id);
        $company->countries()->sync($countries);
        $company->languages()->sync($languages);
    }

    public static function getPermissions($id)
    {
        $company = self::query()->findOrFail($id);
        // $usedPermissions = $company->usedPermissions();
        foreach ($company->permissions as $permission) {
            $permission->setAttribute('name', $permission->translation->value);
            // $permission->pivot->number = $permission->pivot->number - ($usedPermissions->where('permission_id', $permission->pivot->permission_id)->first()  ? $usedPermissions->where('permission_id', $permission->pivot->permission_id)->first()->db : 0);
            $permission->getRelationValue('pivot')->contact = __('common.'.$permission->getRelationValue('pivot')->contact);
        }

        return $company->permissions;
    }

    public static function getSteps($id): array
    {
        $company = self::query()->findOrFail($id);

        $response = [];

        foreach ($company->steps as $step) {
            $temp = [];
            $temp['id'] = $step->id;
            $temp['default_type'] = $step->default_type;
            $temp['type'] = $step->type;
            $temp['name'] = $step->translation->value;
            $temp['values'] = [];
            $values = $step->values;
            $case_input_values = $step->values->sortBy(fn ($case_input_value, $key) => $case_input_value->translation ? $case_input_value->translation->value : null);
            foreach ($case_input_values as $value) {
                $t = [];
                $t['id'] = $value->id;
                $t['value'] = $value->translation->value;
                $temp['values'][] = $value;
            }

            $response[] = $temp;
        }

        return $response;
    }

    public function casesLastMonth()
    {
        $start = new Carbon('first day of last month');
        $start = Carbon::parse($start)->format('yy-m-d');
        $end = new Carbon('last day of last month');
        $end = Carbon::parse($end)->format('yy-m-d');

        return $this->cases()->whereHas('dateFirst', function ($query) use ($start, $end): void {
            $query->whereBetween('case_values.value', [$start, $end]);
        });
    }

    public function casesLastMonthInUsersCountry()
    {
        if (! Auth::user()->all_country && Auth::user()->type != 'admin') {
            return $this->casesLastMonth()->where('cases.country_id', Auth::user()->country_id);
        }

        return $this->casesLastMonth();
    }

    public function riportLastMonth()
    {
        $date = Carbon::now();
        $last_month = $date->subMonthWithNoOverflow();

        return $this->riports()->where('from', $last_month->firstOfMonth()->format('Y-m-d'))->where('to', $last_month->lastOfMonth()->format('Y-m-d'));
    }

    public function getEapMenuVisibilities(): array
    {
        $result = [];
        $menu_item_records = DB::connection('mysql_eap_online')->table('company_menu_item')->where('company_id', $this->id)->get();
        foreach ($menu_item_records as $record) {
            $result[] = $record->menu_item_id;
        }

        return $result;
    }

    public function get_connected_companies()
    {
        $user = auth()->user();

        // Get parent company
        $parentUser = User::query()->where('id', $user->connected_account)->first();
        $parentCompany = ($parentUser) ? $parentUser->companies->first() : null;

        // IF company is connected but it's master is restriced, then it's not allowed to see other companies.
        if (in_array(optional($parentCompany)->id, config('connected-company-country-restriction')) && ! session('masterCompanyAccountId')) {
            return collect([]);
        }

        // Get a collection of companies that are connected to the current company by client user
        $connected_companies = $this->withoutGlobalScope(CountryScope::class)->whereHas('clientUser', function ($query): void {
            $query->where('connected_account', $this->clientUser->first()->id);
        })->get();

        if ($connected_companies->count() <= 0 && ! is_null($user->connected_account)) {
            // Get other companies that are connected to the same parent comapny as the current company.
            $connected_companies = $this->withoutGlobalScope(CountryScope::class)->whereHas('clientUser', function ($query) use ($user): void {
                $query->where('connected_account', $user->connected_account);
            })->get();

            $connected_companies->push($parentCompany);
        } else {
            $connected_companies->push($this);
        }

        return $connected_companies;
    }

    public function is_master_company(): bool
    {
        return $this->withoutGlobalScope(CountryScope::class)->whereHas('clientUser', function ($query): void {
            $query->where('connected_account', $this->clientUser->first()->id);
        })->exists();
    }

    public function activity_plans(): HasMany
    {
        return $this->hasMany(ActivityPlan::class, 'company_id', 'id');
    }

    /**
     * @return Collection<int,CaseInputValue>|null
     * */
    public function get_contact_types_by_permission(int $permission_id): ?Collection
    {
        $contacts = optional($this
            ->permissions()
            ->when($permission_id !== 4, fn (Builder $query) => $query->where('permission_id', $permission_id)) // Allow all consultation type for "Other" permission
            ->first()
        )->getRelationValue('pivot')?->contact;

        if ($contacts) {
            $consultation_types = collect(explode('-', (string) $contacts))->map(fn ($contact): mixed => ConsultationType::fromName(strtoupper($contact)));

            return CaseInputValue::query()
                ->where('case_input_id', 24) // Consultation type
                ->whereIn('id', $consultation_types->pluck('value')->toArray())
                ->with('translation')
                ->get();
        }

        return null;
    }
}
