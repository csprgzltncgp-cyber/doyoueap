<?php

namespace App\Models;

use App\Enums\CaseExpertStatus;
use App\Enums\InvoicingType;
use App\Mail\AssignCaseMail;
use App\Mail\ExpertAfterFirstLoginMail;
use App\Mail\ExpertRegMail;
use App\Models\Scopes\PendingCasesScope;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name Felhasználó neve
 * @property string $email Felhasználó email címe
 * @property string $username Felhasználónév
 * @property Carbon|null $email_verified_at
 * @property string|null $password Felhasználó jelszava
 * @property string|null $type
 * @property string|null $subtype
 * @property int $language_id Megadja, hogy milyen nyelven használja a felhasználó az admint
 * @property int|null $country_id Megadja, hogy melyik országhoz tartozik a felhasználó
 * @property int|null $riport_language_id
 * @property int|null $all_country
 * @property int $all_language
 * @property bool $active
 * @property bool $locked
 * @property int $super_user
 * @property int|null $connected_account
 * @property string|null $remember_token
 * @property string|null $google2fa_secret
 * @property string|null $password_changed_at
 * @property string|null $last_login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, AffiliateSearchCompletionPoint> $affiliate_search_completion_points
 * @property-read int|null $affiliate_search_completion_points_count
 * @property-read Collection<int, AffiliateSearch> $assigned_affiliate_search
 * @property-read int|null $assigned_affiliate_search_count
 * @property-read Collection<int, Task> $assigned_task
 * @property-read int|null $assigned_task_count
 * @property-read Collection<int, Cases> $cases
 * @property-read int|null $cases_count
 * @property-read Collection<int, City> $cities
 * @property-read int|null $cities_count
 * @property-read Collection<int, Company> $companies
 * @property-read int|null $companies_count
 * @property-read Collection<int, ContractHolder> $contractHolders
 * @property-read int|null $contract_holders_count
 * @property-read Country|null $country
 * @property-read Country|null $countryWithOutScope
 * @property-read EapOnlineData|null $eap_online_data
 * @property-read Collection<int, Country> $expertCountries
 * @property-read int|null $expert_countries_count
 * @property-read Collection<int, Country> $expertCrisisCountries
 * @property-read int|null $expert_crisis_countries_count
 * @property-read ExpertData|null $expert_data
 * @property-read Collection<int, InvoiceCaseData> $invoice_case_datas
 * @property-read int|null $invoice_case_datas_count
 * @property-read Collection<int, InvoiceCrisisData> $invoice_crisis_datas
 * @property-read int|null $invoice_crisis_datas_count
 * @property-read InvoiceData|null $invoice_datas
 * @property-read Collection<int, InvoiceOtherActivityData> $invoice_other_activity_datas
 * @property-read int|null $invoice_other_activity_datas_count
 * @property-read Collection<int, InvoiceWorkshopData> $invoice_workshop_datas
 * @property-read int|null $invoice_workshop_datas_count
 * @property-read Collection<int, Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read Language $language
 * @property-read Language $languageWithOutScope
 * @property-read Collection<int, LanguageSkill> $language_skills
 * @property-read int|null $language_skills_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read OpenInvoicing|null $opened_invoicing
 * @property-read OperatorData|null $operator_data
 * @property-read Collection<int, Permission> $permission
 * @property-read int|null $permission_count
 * @property-read Collection<int, Specialization> $specializations
 * @property-read int|null $specializations_count
 * @property-read Collection<int, TaskCompletionPoint> $task_completion_points
 * @property-read int|null $task_completion_points_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User query()
 * @method static Builder|User whereActive($value)
 * @method static Builder|User whereAllCountry($value)
 * @method static Builder|User whereAllLanguage($value)
 * @method static Builder|User whereConnectedAccount($value)
 * @method static Builder|User whereCountryId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereGoogle2faSecret($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLanguageId($value)
 * @method static Builder|User whereLastLoginAt($value)
 * @method static Builder|User whereLocked($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePasswordChangedAt($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRiportLanguageId($value)
 * @method static Builder|User whereSubtype($value)
 * @method static Builder|User whereSuperUser($value)
 * @method static Builder|User whereType($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 *
 * @property-read Collection<int, Consultation> $consultations
 * @property-read int|null $consultations_count
 * @property-read Collection<int, CustomInvoiceItem> $custom_invoice_items
 * @property-read int|null $custom_invoice_items_count
 * @property-read ExpertCurrencyChange|null $expert_currency_changes
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    public $timestamps = true;

    protected $guarded = [];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'locked' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontSubmitEmptyLogs();
    }

    public function setGoogle2faSecretAttribute($value): void
    {
        $this->attributes['google2fa_secret'] = encrypt($value);
    }

    public function getGoogle2faSecretAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function inactivity(): HasOne
    {
        return $this->hasOne(Inactivity::class);
    }

    public function expert_currency_changes(): HasOne
    {
        return $this->hasOne(ExpertCurrencyChange::class, 'user_id', 'id');
    }

    public function invoice_case_datas(): HasMany
    {
        return $this->hasMany(InvoiceCaseData::class, 'expert_id', 'id');
    }

    public function invoice_workshop_datas(): HasMany
    {
        return $this->hasMany(InvoiceWorkshopData::class, 'expert_id', 'id');
    }

    public function invoice_crisis_datas(): HasMany
    {
        return $this->hasMany(InvoiceCrisisData::class, 'expert_id', 'id');
    }

    public function invoice_other_activity_datas(): HasMany
    {
        return $this->hasMany(InvoiceOtherActivityData::class, 'expert_id', 'id');
    }

    public function invoice_live_webinar_datas(): HasMany
    {
        return $this->hasMany(InvoiceLiveWebinarData::class, 'expert_id', 'id');
    }

    public function operator_data(): HasOne
    {
        return $this->hasOne(OperatorData::class, 'user_id', 'id');
    }

    public function permission(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_x_permission');
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'user_x_city');
    }

    public function outsource_countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'outsource_countries');
    }

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(Cases::class, 'expert_x_case', 'user_id', 'case_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function countryWithOutScope(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id')->withoutGlobalScope(CountryScope::class);
    }

    public function languageWithOutScope(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id')->withoutGlobalScope(LanguageScope::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_x_company');
    }

    public function contractHolders(): BelongsToMany
    {
        return $this->belongsToMany(ContractHolder::class, 'user_x_contract_holder');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->whereNull('deleted_by_expert_at');
    }

    public function invoice_datas(): HasOne
    {
        return $this->hasOne(InvoiceData::class);
    }

    public function expert_data(): HasOne
    {
        return $this->hasOne(ExpertData::class);
    }

    public function eap_online_data(): HasOne
    {
        return $this->hasOne(EapOnlineData::class);
    }

    public function task_completion_points(): HasMany
    {
        return $this->hasMany(TaskCompletionPoint::class);
    }

    public function affiliate_search_completion_points(): HasMany
    {
        return $this->hasMany(AffiliateSearchCompletionPoint::class);
    }

    public function expertCountries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'expert_x_country', 'expert_id', 'country_id');
    }

    public function expertCrisisCountries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'expert_x_crisis_country', 'expert_id', 'country_id');
    }

    public function assigned_task(): HasMany
    {
        return $this->hasMany(Task::class, 'to_id', 'id');
    }

    public function assigned_affiliate_search(): HasMany
    {
        return $this->hasMany(AffiliateSearch::class, 'to_id', 'id');
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'user_x_specialization');
    }

    public function language_skills(): BelongsToMany
    {
        return $this->belongsToMany(LanguageSkill::class, 'user_x_language_skill');
    }

    public function opened_invoicing(): HasOne
    {
        return $this->hasOne(OpenInvoicing::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function custom_invoice_items(): HasMany
    {
        return $this->hasMany(CustomInvoiceItem::class, 'user_id', 'id');
    }

    public function workshop_cases(): HasMany
    {
        return $this->hasMany(WorkshopCase::class, 'expert_id', 'id');
    }

    public function crisis_cases(): HasMany
    {
        return $this->hasMany(CrisisCase::class, 'expert_id', 'id');
    }

    public function getNotifications()
    {
        $seenNotifications = NotificationSeen::query()->where('user_id', Auth::user()->id)->pluck('notification_id');
        $notificationUserTargets = NotificationInvidualTarget::query()->where('user_id', Auth::user()->id)
            ->with([
                'notification' => function ($query) use ($seenNotifications): void {
                    $query->where('display_from', '<=', \Carbon\Carbon::now())->whereNotIn('id', $seenNotifications);
                },
            ])
            ->get();
        $notificationUserTargets = $notificationUserTargets->filter(fn ($value, $key) => $value->notification);

        $notificationGroupTargets = NotificationGroupTarget::with([
            'notification' => function ($query) use ($seenNotifications): void {
                $query->where('display_from', '<=', \Carbon\Carbon::now())
                    ->where('display_from', '>=', Auth::user()->created_at)
                    ->whereNotIn('id', $seenNotifications);
            },
            'userTypes',
            'countries',
            'permissions',
        ])->get();

        $notificationGroupTargets = $notificationGroupTargets->filter(function ($value, $key): bool {
            $country_found = 0;
            $userType_found = 0;
            $permission_found = 0;

            /* ORSZÁG ELLENŐRZÉSE */
            if ($value->countries->count()) {
                foreach ($value->countries as $country) {
                    if (Auth::user()->isInCountry($country->country_id)) {
                        $country_found = 1;
                    }
                }
            } else {
                $country_found = 1;
            }

            /* USERTYPE ELLENŐRZÉSE */
            if ($value->userTypes->count()) {
                foreach ($value->userTypes as $userType) {
                    if ($userType->type == 'super_user' && Auth::user()->type == 'admin' && Auth::user()->super_user == 1) {
                        $userType_found = 1;
                    } elseif ($userType->type == Auth::user()->type) {
                        $userType_found = 1;
                    }
                }
            } else {
                $userType_found = 1;
            }
            if ($value->permissions->count()) {
                foreach ($value->permissions as $permissions) {
                    if (in_array($permissions->permission_id, Auth::user()->permission->pluck('id')->toArray())) {
                        $permission_found = 1;
                    }
                }
            } else {
                $permission_found = 1;
            }

            return ! empty($value->notification) && $country_found && $userType_found && $permission_found;
        });
        $notifications = $notificationUserTargets->merge($notificationGroupTargets);
        $notifications = $notifications->sortBy('notification.display_from');

        return $notifications->mapWithKeys(function ($item): array {
            /** @var NotificationGroupTarget|NotificationInvidualTarget $item */
            if ($item->notification->translation !== null) {
                return [$item->notification_id => $item->notification->translation !== null ? $item->notification->translation->value : null];
            }

            return [];
        });
    }

    public function toggleActive(): void
    {
        $this->active = ! $this->active;
        $this->save();
    }

    public function toggleLocked(): void
    {
        $this->locked = ! $this->locked;
        $this->save();
    }

    public static function assignCase(int $case_id, int $expert_id)
    {
        $user = self::query()->findOrFail($expert_id);
        $case = Cases::query()->withoutGlobalScope(PendingCasesScope::class)->findOrFail($case_id);
        $case->status = 'assigned_to_expert';
        $case->save();
        // minden olyan expert hozzárendelést törlünk, ahol -1 az accepted
        $case->experts()->wherePivotIn('accepted', [CaseExpertStatus::ASSIGNED_TO_EXPERT->value, CaseExpertStatus::ACCEPTED->value])->detach();
        $case->experts()->attach($expert_id);

        Mail::to($user->email)->send(new AssignCaseMail($user, $case));

        return $user;
    }

    public static function assign_single_session_case($request)
    {
        $user = self::query()->findOrFail($request->expert_id);
        $case = Cases::query()->withoutGlobalScope(PendingCasesScope::class)->findOrFail($request->case_id);
        $case->status = 'confirmed';
        $case->confirmed_by = $user->id;
        $case->confirmed_at = Carbon::now('Europe/Budapest');
        $case->customer_satisfaction = $request->customer_satisfaction;
        $case->save();
        $case->experts()->detach();
        $case->experts()->attach($request->expert_id);

        // Create on consultation
        $case->consultations()->create([
            'user_id' => $user->id,
            'permission_id' => (int) $case->case_type->value,
            'created_at' => Carbon::now(),
        ]);

        // Create invoice case data
        InvoiceCaseData::query()->firstOrCreate([
            'case_identifier' => $case->case_identifier,
            'consultations_count' => 1,
            'expert_id' => $user->id,
            'duration' => 50,
            'permission_id' => (int) $case->case_type->value,
        ]);

        return $user;
    }

    /* OPERATOR */
    protected static function store_operator(array $data)
    {
        $user = new self;
        $user->type = $data['type'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'];
        $user->country_id = $data['country_id'];
        $user->language_id = $data['language_id'];
        $password = Hash::make($data['password']);
        $user->password = $password;
        $user->save();
    }

    protected function edit_operator($id, array $data, $additional_data = null)
    {
        if (! empty($additional_data)) {
            $operator_data = OperatorData::query()->firstOrCreate([
                'user_id' => $id,
            ]);

            foreach ($additional_data as $key => $value) {
                $operator_data->update([
                    $key => $value,
                ]);
            }
        }

        self::query()->where('id', $id)->update($data);
    }
    /* OPERATOR */

    /* ADMIN */
    public static function store_admin($data): User
    {
        $user = new self;
        $user->type = $data['type'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'];
        $user->country_id = $data['country_id'];
        $user->language_id = $data['language_id'];
        $pass = $data['password'];
        $user->password = Hash::make($pass);
        $user->connected_account = $data['connected_account'] ?? null;
        $user->type = $data['type'];
        $user->save();

        return $user;
    }

    public static function edit_admin($id, array $data): void
    {
        self::query()->where('id', $id)->update($data);
    }
    /* ADMIN */

    /* MAILS */
    public function sendCreatedMail(): void
    {
        Mail::to($this->email)->send(new ExpertRegMail($this));
    }

    public function sendAfterFirstLoginMail(): void
    {
        Mail::to($this->email)->send(new ExpertAfterFirstLoginMail($this));
    }

    public static function resendRegMail($id): void
    {
        $user = self::query()->findOrFail($id);
        if (! $user->last_login_at) {
            Mail::to($user->email)->send(new ExpertRegMail($user));
        }
    }
    /* MAILS */

    public function hasPermission($id)
    {
        return $this->belongsToMany(Permission::class, 'user_x_permission')->where('permission_id', $id)->count();
    }

    public function hasPermissions($permissionIds)
    {
        return $this->belongsToMany(Permission::class, 'user_x_permission')->whereIn('permission_id', $permissionIds)->count();
    }

    public function has_missing_expert_data(): bool
    {
        if (empty($this->expert_data)) {
            return true;
        }

        if (empty($this->email) || $this->email == 'null') {
            return true;
        }

        if (empty($this->expert_data->phone_prefix) || $this->expert_data->phone_prefix == 'null') {
            return true;
        }

        if (empty($this->expert_data->phone_number) || $this->expert_data->phone_number == 'null') {
            return true;
        }

        if (! $this->expert_data->is_cgp_employee) {
            if ($this->expert_data->required_documents && ! $this->expert_data->completed_first) {
                if (! $this->expert_data->files()->where('type', ExpertFile::TYPE_CERTIFICATE)->exists()) {
                    return true;
                }

                if (! $this->expert_data->files()->where('type', ExpertFile::TYPE_CONTRACT)->exists()) {
                    return true;
                }
            }

            if (empty($this->expert_data->post_code) || $this->expert_data->post_code == 'null') {
                return true;
            }

            if (empty($this->expert_data->city_id) || $this->expert_data->city_id == 'null') {
                return true;
            }

            if (empty($this->expert_data->country_id) || $this->expert_data->country_id == 'null') {
                return true;
            }

            if (empty($this->expert_data->street) || $this->expert_data->street == 'null') {
                return true;
            }

            if (empty($this->expert_data->street_suffix) || $this->expert_data->street_suffix == 'null') {
                return true;
            }

            if (empty($this->expert_data->house_number) || $this->expert_data->house_number == 'null') {
                return true;
            }
        }

        if ($this->hasPermission(1) && ! $this->specializations->count()) {
            return true;
        }

        if (in_array($this->id, [830, 878, 632, 826, 999, 809, 989, 39, 835, 834, 837, 877])) {
            if (is_null($this->invoice_datas->hourly_rate_50)) {
                return true;
            }

            if (empty($this->invoice_datas->currency)) {
                return true;
            }

            if (($this->hasPermission(2) || $this->hasPermission(3) || $this->hasPermission(7)) && is_null($this->invoice_datas->hourly_rate_30)) {
                return true;
            }
        } else {
            if (is_null($this->invoice_datas->hourly_rate_50) && $this->invoice_datas->invoicing_type === InvoicingType::TYPE_NORMAL) {
                return true;
            }

            if (empty($this->invoice_datas->currency)) {
                return true;
            }

            if (
                ($this->hasPermission(2) || $this->hasPermission(3) || $this->hasPermission(7))
                &&
                (empty($this->invoice_datas->hourly_rate_30) || $this->invoice_datas->hourly_rate_30 == 'null')
                &&
                $this->invoice_datas->invoicing_type === InvoicingType::TYPE_NORMAL
            ) {
                return true;
            }
        }

        return false;
    }

    public function isHungarian(): int
    {
        return in_array(1, $this->expertCountries->pluck('pivot.country_id')->toArray()) ? 1 : 0;
    }

    public function isInCountry($id): int
    {
        if ($this->type == 'expert') {
            $countries = array_merge($this->expertCountries->pluck('pivot.country_id')->toArray(), $this->expertCrisisCountries->pluck('pivot.country_id')->toArray());

            return in_array($id, $countries) ? 1 : 0;
        }

        return in_array($id, $this->hasConnectedAccounts()->pluck('country_id')->toArray()) ? 1 : 0;
    }

    public function hasConnectedAccounts()
    {
        return self::query()->whereRaw('
      (users.id = ?) OR
      (users.id = ?) OR
      (users.connected_account = ? && users.connected_account IS NOT NULL) OR
      (users.connected_account = ? && users.connected_account IS NOT NULL)
      ', [Auth::user()->id, Auth::user()->connected_account, Auth::user()->connected_account, Auth::user()->id])
            ->with('countryWithOutScope')->get();
    }

    public function hasConnectedClientAccounts()
    {
        if (empty(Auth::user()->contractHolders)) {
            return null;
        }

        if (empty(Auth::user()->companies()->first())) {
            return null;
        }

        return Auth::user()->companies()->first()->clientUsers()->with('countryWithOutScope')->whereNull('users.subtype')->get();
    }

    public function setCrisisPsychologist($crisisPsychologist): void
    {
        $this->expert_data->crisis_psychologist = $crisisPsychologist ? 1 : 0;

        $this->expert_data->save();
    }
    /* EXPERT */

    public function old_passwords(): HasMany
    {
        return $this->hasMany(OldPassword::class, 'user_id', 'id');
    }
}
