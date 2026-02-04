<?php

namespace App\Models;

use App\Enums\CaseExpertStatus;
use App\Enums\CompsychSurveyType;
use App\Mail\CrisisCaseMail;
use App\Mail\PendingCaseMail;
use App\Mail\Pulso\IntakeEmail;
use App\Mail\QuestionCopyToExpert;
use App\Mail\QuestionToCountryEmail;
use App\Mail\QuestionToOperator;
use App\Models\Scopes\PendingCasesScope;
use App\Scopes\CountryScope;
use App\Services\CompsychSurveyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * App\Models\Cases
 *
 * @property int $id
 * @property string $case_identifier
 * @property string $status
 * @property int $company_id Megadja, hogy melyik céghez tartozik az eset
 * @property int $country_id Megadja, hogy melyik országhoz tartozik az eset
 * @property string|null $employee_contacted_at
 * @property int|null $confirmed_by Megadja, hogy ki hagyta jóvá
 * @property string|null $confirmed_at Megadja, hogy mikor hagyták jóvá
 * @property bool $wos_survey_clicked
 * @property int|null $phq9_opening
 * @property int|null $phq9_closing
 * @property int $nestle_questionnaire_sent
 * @property int|null $eap_consultation_deleted
 * @property int $created_by megadja, hogy ki hozta létre
 * @property string|null $activity_code
 * @property int|null $customer_satisfaction
 * @property int|null $customer_satisfaction_not_possible
 * @property int|null $closed_by_expert
 * @property int $email_sent_3months Kiküldtük-e a 3 hónapos emailt
 * @property int $email_sent_5days
 * @property int $email_sent_24hours
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read CaseValues|null $case_client_language
 * @property-read CaseValues|null $case_client_name
 * @property-read CaseValues|null $case_language_skill
 * @property-read CaseValues|null $case_location
 * @property-read CaseValues|null $case_presenting_concern
 * @property-read CaseValues|null $case_specialization
 * @property-read CaseValues|null $case_type
 * @property-read Company $company
 * @property-read Collection<int, Consultation> $consultations
 * @property-read int|null $consultations_count
 * @property-read Country $country
 * @property-read Collection<int, CaseValues> $date
 * @property-read int|null $date_count
 * @property-read CaseValues|null $dateFirst
 * @property-read Collection<int, User> $experts
 * @property-read int|null $experts_count
 * @property-read Consultation|null $firstConsultation
 * @property-read mixed $permission_count
 * @property-read CaseValues|null $is_case_crisis
 * @property-read User $operator
 * @property-read Collection<int, CaseValues> $values
 * @property-read int|null $values_count
 * @property-read Collection<int, WosAnswers> $wos_answers
 * @property-read int|null $wos_answers_count
 *
 * @method static Builder|Cases newModelQuery()
 * @method static Builder|Cases newQuery()
 * @method static Builder|Cases onlyTrashed()
 * @method static Builder|Cases query()
 * @method static Builder|Cases whereActivityCode($value)
 * @method static Builder|Cases whereCaseIdentifier($value)
 * @method static Builder|Cases whereClosedByExpert($value)
 * @method static Builder|Cases whereCompanyId($value)
 * @method static Builder|Cases whereConfirmedAt($value)
 * @method static Builder|Cases whereConfirmedBy($value)
 * @method static Builder|Cases whereCountryId($value)
 * @method static Builder|Cases whereCreatedAt($value)
 * @method static Builder|Cases whereCreatedBy($value)
 * @method static Builder|Cases whereCustomerSatisfaction($value)
 * @method static Builder|Cases whereCustomerSatisfactionNotPossible($value)
 * @method static Builder|Cases whereDeletedAt($value)
 * @method static Builder|Cases whereEapConsultationDeleted($value)
 * @method static Builder|Cases whereEmailSent24hours($value)
 * @method static Builder|Cases whereEmailSent3months($value)
 * @method static Builder|Cases whereEmailSent5days($value)
 * @method static Builder|Cases whereEmployeeContactedAt($value)
 * @method static Builder|Cases whereId($value)
 * @method static Builder|Cases whereNestleQuestionnaireSent($value)
 * @method static Builder|Cases wherePhq9Closing($value)
 * @method static Builder|Cases wherePhq9Opening($value)
 * @method static Builder|Cases whereStatus($value)
 * @method static Builder|Cases whereUpdatedAt($value)
 * @method static Builder|Cases whereWosSurveyClicked($value)
 * @method static Builder|Cases withTrashed()
 * @method static Builder|Cases withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Cases extends Model
{
    use SoftDeletes;

    protected $company_input_id;

    protected $case_type_id;

    protected $case_specialization_id;

    protected $case_language_skill_id;

    protected $case_location_id;

    protected $case_client_name_id;

    protected $case_is_crisis_id;

    protected $case_presenting_concern_id;

    protected $case_clients_language_id;

    protected $fillable = ['confirmed_by', 'confirmed_at', 'status'];

    protected $table = 'cases';

    protected $casts = [
        'wos_survey_clicked' => 'boolean',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->company_input_id = optional(CaseInput::query()->where('default_type', 'case_creation_time')->first())->id;
        $this->case_type_id = optional(CaseInput::query()->where('default_type', 'case_type')->first())->id;
        $this->case_location_id = optional(CaseInput::query()->where('default_type', 'location')->first())->id;
        $this->case_client_name_id = optional(CaseInput::query()->where('default_type', 'client_name')->first())->id;
        $this->case_is_crisis_id = optional(CaseInput::query()->where('default_type', 'is_crisis')->first())->id;
        $this->case_presenting_concern_id = optional(CaseInput::query()->where('default_type', 'presenting_concern')->first())->id;
        $this->case_clients_language_id = optional(CaseInput::query()->where('default_type', 'clients_language')->first())->id;
        $this->case_specialization_id = optional(CaseInput::query()->where('default_type', 'case_specialization')->first())->id;
        $this->case_language_skill_id = optional(CaseInput::query()->where('default_type', 'case_language_skill')->first())->id;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CountryScope);
        static::addGlobalScope(new PendingCasesScope);

        static::deleting(function (Cases $case): void {
            foreach ($case->values as $value) {
                $value->delete();
            }

            $consultations = Consultation::withTrashed()->where('case_id', $case->id)->get();

            foreach ($consultations as $consultation) {
                $consultation->forceDelete();
            }

            $case->experts()->detach();
        });
    }

    public function getStatusAttribute($value): ?string
    {
        return match ($value) {
            'pending' => 'Függőben',
            'opened' => 'Új',
            'assigned_to_expert' => 'Szakértőhöz kiközvetítve',
            'employee_contacted' => 'Kapcsolatfelvétel megtörtént',
            'client_unreachable' => 'A kliens elérhetetlen!',
            'confirmed' => 'Lezárt',
            'client_unreachable_confirmed' => 'Kliens elérhetetlen lezárva',
            'interrupted' => 'A tanácsadás megszakadt',
            'interrupted_confirmed' => 'A tanácsadás megszakadt lezárva',
            default => null,
        };
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function values(): HasMany
    {
        return $this->hasMany(CaseValues::class, 'case_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function experts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'expert_x_case', 'case_id', 'user_id')->withPivot('accepted', 'created_at', 'id')->orderBy('expert_x_case.id', 'desc')->withTimestamps();
    }

    public function date(): HasMany
    {
        return $this->hasMany(CaseValues::class, 'case_id')->where('case_input_id', $this->company_input_id);
    }

    public function dateFirst(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->company_input_id);
    }

    public function case_type(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_type_id);
    }

    public function case_specialization(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_specialization_id);
    }

    public function case_language_skill(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_language_skill_id);
    }

    public function case_location(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_location_id);
    }

    public function case_presenting_concern(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_presenting_concern_id);
    }

    public function is_case_crisis(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_is_crisis_id);
    }

    public function case_client_language(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_clients_language_id);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'case_id');
    }

    public function firstConsultation(): HasOne
    {
        return $this->hasOne(Consultation::class, 'case_id');
    }

    public function case_client_name(): HasOne
    {
        return $this->hasOne(CaseValues::class, 'case_id')->where('case_input_id', $this->case_client_name_id);
    }

    public function wos_answers(): HasMany
    {
        return $this->hasMany(WosAnswers::class, 'case_id');
    }

    public function isCaseNotAccepted()
    {
        return $this->belongsToMany(User::class, 'expert_x_case', 'case_id', 'user_id')->withPivot('accepted')->orderBy('id', 'desc')->where('user_id', Auth::user()->id)->where('accepted', CaseExpertStatus::REJECTED->value);
    }

    public function isMyCase()
    {
        return $this->belongsToMany(User::class, 'expert_x_case', 'case_id', 'user_id')->withPivot('accepted')->orderBy('id', 'desc')->where('user_id', Auth::user()->id)->where('accepted', CaseExpertStatus::ACCEPTED->value);
    }

    public static function filter(
        ?array $attributes = null,
        ?array $inputs = null,
        ?int $expert = null,
        ?int $contract_holder_id = null,
        ?int $org_id = null,
        ?int $activity_code = null,
        ?string $consultation_date_from = null,
        ?string $consultation_date_to = null,
        ?string $case_confirmed_at_from = null,
        ?string $case_confirmed_at_to = null
    ) {
        $cases = self::query()->orderBy('id', 'desc')->with(['values', 'values.input', 'experts', 'consultations']);

        if (! Auth::user()->type == 'admin') {
            $cases = $cases->whereNotIn('status', ['confirmed', 'client_unreachable_confirmed', 'interrupted_confirmed']);
        }

        if ($contract_holder_id) {
            $companies = Company::query()->whereHas('org_datas', fn ($query) => $query->where('contract_holder_id', $contract_holder_id))->pluck('id');
            $cases = $cases->whereIn('cases.company_id', $companies);
        }

        if ($org_id) {
            $companies = Company::query()->where('orgId', $org_id)->pluck('id');
            $cases = $cases->whereIn('cases.company_id', $companies);
        }

        if ((int) $activity_code !== 0) {
            $cases = $cases->whereNotNull('activity_code');
        }

        if ($consultation_date_from !== null && $consultation_date_from !== '' && $consultation_date_from !== '0') {
            $cases = $cases
                ->whereHas('consultations', function ($q) use ($consultation_date_from, $consultation_date_to): void {
                    $q->where('created_at', '>=', Carbon::parse($consultation_date_from)->startOfDay());
                    $q->when($consultation_date_to !== null && $consultation_date_to !== '' && $consultation_date_to !== '0', fn ($q) => $q->where('created_at', '<=', Carbon::parse($consultation_date_to)->endOfDay()));
                });

        } elseif ($consultation_date_to !== null && $consultation_date_to !== '' && $consultation_date_to !== '0') {
            $cases = $cases->whereHas('consultations', fn ($q) => $q->where('created_at', '<=', Carbon::parse($consultation_date_to)->endOfDay()));
        }

        if ($case_confirmed_at_from !== null && $case_confirmed_at_from !== '' && $case_confirmed_at_from !== '0') {
            $cases = $cases
                ->where('confirmed_at', '>=', Carbon::parse($case_confirmed_at_from)->startOfDay())
                ->when($case_confirmed_at_to !== null && $case_confirmed_at_to !== '' && $case_confirmed_at_to !== '0', fn ($q) => $q->where('confirmed_at', '<=', Carbon::parse($case_confirmed_at_to)->endOfDay()));

        } elseif ($case_confirmed_at_to !== null && $case_confirmed_at_to !== '' && $case_confirmed_at_to !== '0') {
            $cases = $cases->where('confirmed_at', '<=', Carbon::parse($case_confirmed_at_to)->startOfDay());
        }

        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $cases = $cases->where($key, $value);
            }
        }
        if ($expert) {
            $cases = $cases->whereHas('experts', function (Builder $query) use ($expert): void {
                $query->where('expert_x_case.user_id', $expert)->where('expert_x_case.accepted', CaseExpertStatus::ACCEPTED->value);
            });
        }

        if ($inputs) {
            $case_values = CaseValues::query()->select('*');
            foreach ($inputs as $key => $value) {
                // ha nincs FROM és TO, tehát nem dátum
                if (! isset($value['from']) && ! isset($value['to'])) {

                    $case_values = $case_values->orWhere(function ($query) use ($value, $key): void {
                        foreach ($value as $k => $v) {
                            $query->where('case_input_id', $key)->where(function ($q) use ($k, $v): void {
                                $q = $k == 0 ? $q->where('value', 'LIKE', '%'.$v.'%') : $q->orWhere('value', 'LIKE', '%'.$v.'%');
                            });
                        }
                    });
                } // ha van FROM és TO
                else {
                    $from = isset($value['from']) ? Carbon::parse($value['from'])->format('Y-m-d') : Carbon::parse('1970-01-01 00:00:00');
                    $to = isset($value['to']) ? Carbon::parse($value['to'])->format('Y-m-d') : Carbon::parse('2300-01-01 00:00:00');

                    $case_values = $case_values->orWhere(function ($query) use ($key, $from, $to): void {
                        $query->where('case_input_id', $key)->where(function ($q) use ($from, $to): void {
                            $q = $q->whereBetween('value', [$from, $to]);
                        });
                    });
                }
            }

            $case_ids = $case_values
                ->groupBy('case_id')
                ->select('case_id', DB::raw('count(case_id) as count'))
                ->get();

            $case_ids = $case_ids->filter(fn ($value, $key): bool => $value['count'] >= count($inputs))->pluck('case_id');

            $cases = $cases->whereIn('cases.id', $case_ids);
        }

        return $cases;
    }

    public static function createFilterQueryString($array): string
    {
        $query_string = '';
        // hozzáfűzzük azokat, amik a case attribútumai (tehát szereplnek a cases táblában)
        foreach ($array['attributes'] as $key => $value) {
            foreach ($value as $v) {
                if ($v == -1) {
                    continue;
                }
                if ($v == '') {
                    continue;
                }
                $query_string .= 'attributes['.$key.']'.'='.$v.'&';
            }
        }

        $query_string = substr($query_string, 0, strlen($query_string) - 1);

        // hozzáfűzzük az inputokat
        foreach ($array['filter'] ?? [] as $key => $value) {
            // DATE-ről van szó
            if (isset($value['from'])) {
                $i = 1;
                foreach ($value['from'] as $v) {
                    if ($v) {
                        if ($query_string !== '') {
                            $query_string .= '&inputs['.$key.'][from]=';
                        } else {
                            $query_string .= 'inputs['.$key.'][from]=';
                        }
                        $query_string .= $v;
                        $i++;
                    }
                }
                $i = 1;
                foreach ($value['to'] as $v) {
                    if ($v) {
                        if ($query_string !== '') {
                            $query_string .= '&inputs['.$key.'][to]=';
                        } else {
                            $query_string .= 'inputs['.$key.'][to]=';
                        }
                        $query_string .= $v;
                        $i++;
                    }
                }
            } // nem DATE-ről van szó
            else {
                $i = 1;
                foreach ($value['value'] as $v) {
                    if ($v && $v != -1) {
                        if ($query_string !== '') {
                            $query_string .= '&inputs['.$key.']['.$i.']=';
                        } else {
                            $query_string .= 'inputs['.$key.']['.$i.']=';
                        }
                        $query_string .= $v;
                        $i++;
                    }
                }
            }
        }

        if (isset($array['expert']) && $array['expert'] != -1) {
            $query_string .= '&expert='.$array['expert'];
        }

        if (isset($array['contract_holder_id']) && $array['contract_holder_id'] != -1) {
            $query_string .= '&contract_holder_id='.$array['contract_holder_id'];
        }

        if (isset($array['OrgId']) && $array['OrgId'] != -1) {
            $query_string .= '&orgId='.$array['OrgId'];
        }

        if (isset($array['activity_code']) && $array['activity_code'] != -1) {
            $query_string .= '&activity_code='.$array['activity_code'];
        }

        if (isset($array['show_consultation_numbers_total']) && $array['show_consultation_numbers_total'] != -1) {
            $query_string .= '&show_consultation_numbers_total='.$array['show_consultation_numbers_total'];
        }

        if (isset($array['consultation_date_from']) && $array['consultation_date_from'] != '') {
            $query_string .= '&consultation_date_from='.$array['consultation_date_from'];
        }

        if (isset($array['consultation_date_to']) && $array['consultation_date_to'] != '') {
            $query_string .= '&consultation_date_to='.$array['consultation_date_to'];
        }

        if (isset($array['case_confirmed_at_from']) && $array['case_confirmed_at_from'] != '') {
            $query_string .= '&case_confirmed_at_from='.$array['case_confirmed_at_from'];
        }

        if (isset($array['case_confirmed_at_to']) && $array['case_confirmed_at_to'] != '') {
            $query_string .= '&case_confirmed_at_to='.$array['case_confirmed_at_to'];
        }

        return $query_string;
    }

    public static function getExportData($cases): array
    {
        $array = [];
        $header = [];

        self::query()
            ->with([
                'values.input.translation',
                'consultations',
            ])
            ->whereIn('id', $cases)
            ->chunk(50, function ($cases_chunk) use (&$array, &$header): void {
                foreach ($cases_chunk as $case) {
                    $temp = [];

                    $temp['Azonosító'] = $case->case_identifier;
                    $header['Azonosító'] = true;

                    foreach ($case->values as $value) {
                        $label = $value->input->translation->value;
                        $header[$label] = true;
                        $temp[$label] = $value->getValue();
                    }

                    if ($case->activity_code) {
                        $header['Activity code'] = true;
                        $temp['Activity code'] = $case->activity_code;
                    }

                    if ($expert = $case->case_accepted_expert()) {
                        $header['Kiközvetített szakértő'] = true;
                        $temp['Kiközvetített szakértő'] = $expert->name;
                    }

                    $header['Ülések száma'] = true;
                    $temp['Ülések száma'] = $case->consultations->count();

                    $contract_holder_id = $case->company->org_datas->where('country_id', $case->country_id)->first()?->contract_holder_id;

                    if ($contract_holder_id) {
                        $header['Szerződés jogosultja'] = true;
                        $temp['Szerződés jogosultja'] = ContractHolder::query()->find($contract_holder_id)->name;
                    }

                    $array[] = $temp;
                }
            });

        return [
            'data' => $array,
            'header' => array_keys($header),
        ];

    }

    public function getAvailableExperts($skip_ids = null)
    {
        if (optional(Auth::user())->type === 'eap_admin' || optional(Auth::user())->type === 'admin') {
            return User::query()
                ->where('type', 'expert')
                ->where('active', 1)
                ->where('locked', 0)
                ->orderBy('name')
                ->get();
        }

        $language_skill_id = optional($this->case_language_skill)->value ?? (int) optional($this->case_client_language)->value;

        return query_available_experts(
            is_crisis: $this->is_case_crisis->value == 1,
            permission_id: (int) $this->case_type->value,
            country_id: (int) $this->country_id,
            city_id: (int) $this->values->where('case_input_id', 5)->first()->value,
            specialization_id: (int) optional($this->case_specialization)->value,
            language_skill_id: $language_skill_id,
            consultation_minute: (int) $this->values->where('case_input_id', 22)->first()->value,
            is_personal: $this->values->where('case_input_id', 24)->first()->value == 80,
            case: $this,
            skip_ids: $skip_ids,
            company_id: $this->company_id,
            problem_details: (int) $this->values->where('case_input_id', 16)->first()->value,
        );
    }

    public static function editCase($id, $request): void
    {
        foreach ($request->inputs as $key => $value) {
            $temp = [
                'case_input_id' => $key,
                'value' => $value,
            ];
            CaseValues::query()->where('case_id', $id)->where('case_input_id', $key)->update(['value' => $value]);
        }
    }

    public static function createCase($request): ?self
    {
        $company = CaseInput::query()->where('default_type', 'company_chooser')->select('id')->first();
        CaseInput::query()->where('default_type')->select('id')->first();

        /*
         * Check if application code is required and if the code is valid
         */
        if ($application_code_required = self::check_application_code_requirement($request->inputs[$company->id])) {
            if (! $request->get('application_code')) {
                return null;
            }

            if (! self::check_application_code($request->application_code, $request->inputs[$company->id])) {
                return null;
            }
        }

        /*
         * Check if email(input 18) is valid
         */
        if (! filter_var($request->inputs[18], FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        /*
         * When company is Deutsche Telekom IT Solutions / DT-ITS email (1165)
         *
         * Check if email exitst and was used 3 times.
         */
        if ($request->inputs[$company->id] == config('companies.deutsche-telekom')) {
            $email = DeutscheTelekomEmail::query()
                ->where('email', Str::of((string) $request->inputs[18])->lower()->trim()) // 18 - email case input id
                ->first();

            if ($email && $email->case_id_1 && $email->case_id_2 && $email->case_id_3) {

                // Return null to prevent showing the case created page
                return null;
            }
        }

        $case = new self;

        /*
         * When company is Deutsche Telekom IT Solutions / DT-ITS email (1165)
         *
         * Set the default status to pending
         */
        $case->status = ((int) $request->inputs[$company->id] === config('companies.deutsche-telekom')) ? 'pending' : 'opened';
        $case->company_id = $request->inputs[$company->id];
        $case->country_id = Auth::user()->country_id;
        $case->created_by = Auth::user()->id;
        $case->case_identifier = (string) random_int(10_000_000, 99_999_999);
        $case->save();

        $array = [];
        foreach ($request->inputs as $key => $value) {
            $temp = [
                'case_id' => $case->id,
                'case_input_id' => $key,
                'value' => $value,
                'created_at' => Carbon::now(),
            ];
            $array[] = $temp;
        }

        // if crisis call, send email to the backoffice manager
        if (array_key_exists(3, $request->inputs) && (int) $request->inputs[3] === 1) {
            Mail::to('maria.szabo@cgpeu.com')->send(new CrisisCaseMail($case->case_identifier));
        }

        CaseValues::query()->insert($array);

        /*
         * Assign available expert to case
         *
         * EXCEPT: When single session therapy(17) and when company is NOT Deutsche Telekom IT Solutions / DT-ITS (1165) and NOT Telus/Lifeworks country
         */
        if ((int) $case->case_type->value !== 17 && (int) $request->inputs[$company->id] !== config('companies.deutsche-telekom') && ! in_array(auth()->user()->country_id, config('lifeworks-countries'))) {
            $experts = $case->getAvailableExperts();

            if ($experts->isNotEmpty()) {
                User::assignCase($case->id, $experts->first()->id);
            }
        }

        /*
         * When company is Deutsche Telekom IT Solutions / DT-ITS email (1165)
         *
         * Send the confirmation email to the employee
         */
        if ($case->company_id == config('companies.deutsche-telekom')) {
            try {
                Mail::to($request->inputs[18])->send(new PendingCaseMail(
                    name: $request->inputs[4], // 4 - is the case input id of name
                    email: $request->inputs[18], // 18 - is the case input id of the email
                    case: $case,
                ));

                // Return null to prevent showing the case created page
                return null;
            } catch (Exception $e) {
                Log::error('Error sending Deutsche Telekom confirmation email: '.$e->getMessage());
            }
        }

        /*
         * When application code is required add case id to the code
         */
        if ($application_code_required) {
            self::store_application_code($request->get('application_code'), $case->id);
        }

        if ((int) $case->company->org_datas->first()->contract_holder_id === 3 && (int) $request->inputs[7] === 1) {
            $compsych_survey_form_service = new CompsychSurveyService(CompsychSurveyType::CASE_CREATED);

            $compsych_survey_form_service->send_mail($request->inputs[4], $request->inputs[18], $case->case_identifier);
        }

        try {
            $email = CaseInput::query()->where('default_type', 'client_email')->select('id')->first();

            /* Pulso Intake emails to specific companies */
            $language_input_id = CaseInput::query()->where('id', 65)->first();

            if (array_key_exists($language_input_id->id, $request->inputs)) {
                $language_value_id = optional(CaseInputValue::query()->where('id', (int) $request->inputs[$language_input_id->id])->first())->id;
            } else {
                $language_value_id = null;
            }

            $language = get_country_code_from_client_language($language_value_id);
            // Ab Inbev - cz, ua
            if ((int) $case->company_id == 199 && in_array((int) $case->country_id, [3, 20])) {
                Mail::to($request->inputs[$email->id])->send(new IntakeEmail($language, $case->case_identifier));

                return $case;
            }

            // EUROFIT GROUP - hu, sk
            if ((int) $case->company_id == 621 && in_array((int) $case->country_id, [1, 4])) {
                Mail::to($request->inputs[$email->id])->send(new IntakeEmail($language, $case->case_identifier));

                return $case;
            }

            // UCB - pl, bg, ro, hu, sk, cz
            if ((int) $case->company_id == 705 && in_array((int) $case->country_id, [2, 9, 6, 1, 4, 3])) {
                Mail::to($request->inputs[$email->id])->send(new IntakeEmail($language, $case->case_identifier));

                return $case;
            }

            return $case;
        } catch (Exception $e) {
            Log::error('Error sending intake email: '.$e->getMessage());

            return $case;
        }
    }

    public function isCloseable($user = null, $online_therapy_booking = false): array
    {
        if (empty($user)) {
            $user = Auth::user();
        }

        if (! $user) {
            return [
                'closeable' => 0,
                'details' => [
                    'user' => 0,
                ],
            ];
        }

        /* ESET ÉRTÉKEK VIZSGÁLATA */
        $details = [];

        foreach ($this->values as $value) {
            if ((empty($value->input->company_id) || $value->input->company_id == $this->company_id) && (! in_array($value->case_input_id, [2, 5, 6, 8, 9, 12, 13, 36, 28, 54, 64, 66, 96, 97]) && empty($value->value) && $value->input->id != 17)) {
                $details[] = 'case_input_'.$value->input->id.'_field';
            }

            // Pszichologiai eseteknél (1) nem lezárható HA:
            // Nincs specializáció megadva
            if ($this->case_type->value != 1) {
                continue;
            }
            if (! ($value->case_input_id == 66 && empty($value->value))) {
                continue;
            }

            $details[] = 'case_input_'.$value->input->id.'_field';
        }

        if ($details !== []) {
            return [
                'closeable' => 0,
                'details' => $details,
            ];
        }

        // csak az utlolsó konzultáció után lehet lezárni
        $last_consultation = $this->consultations()->orderBy('created_at', 'desc')->first();

        if (Carbon::parse(optional($last_consultation)->created_at)->gt(Carbon::now())) {
            return [
                'closeable' => 0,
                'details' => [
                    'last_consultation' => 0,
                    'last_consultation_date' => Carbon::parse($last_consultation->created_at)->format('Y-m-d H:i'),
                ],
            ];
        }

        $case_type = optional($this->case_type)->value;

        /* COACHING ESETEKNÉL */
        if ($case_type == 11 && $this->customer_satisfaction_not_possible) {
            return [
                'closeable' => 1,
            ];
        }

        /* MUNKAJOGI ESETKENÉL VAGY EGYÉB ESETKNÉL */
        if (in_array($case_type, [5, 4]) && (
            $this->getRawOriginal('status') == 'employee_contacted' ||
            $this->customer_satisfaction_not_possible ||
            $this->customer_satisfaction
        )) {
            return [
                'closeable' => 1,
            ];
        }

        // Az LPP cég esetén az elégedettségi pontszám kitöltése nem szükséges
        if (in_array($case_type, [5, 4]) && ! (
            $this->getRawOriginal('status') == 'employee_contacted' ||
            $this->customer_satisfaction_not_possible ||
            $this->customer_satisfaction
        ) && $this->company_id != 843) {
            return [
                'closeable' => 0,
                'details' => [
                    'customer_satisfaction' => 0,
                ],
            ];
        }

        if (! in_array($this->getRawOriginal('status'), ['interrupted', 'interrupted_confirmed', 'client_unreachable', 'client_unreachable_confirmed'])) {
            /* HA OPTUM A CONTRACT HOLDER ÉS PSZICHOLÓGIAI AZ ESET ÉS CZ SR VAGY RO AZ ORSZÁG AKKOT KI KELL TÖLTENI A PHQ9 KÉRDŐÍVET */

            if (empty($this->company) && ! $this->company()->withoutGlobalScope(CountryScope::class)->exists()) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'company' => 0,
                    ],
                ];
            }

            if ($this->case_company_contract_holder() === 4 && empty($this->phq9_closing) && (int) $this->case_type->value === 1 && in_array((int) $this->country_id, [3, 7, 6])) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'phq9_closing' => 0,
                    ],
                ];
            }

            // sr, cz, ro
            if ($this->case_company_contract_holder() === 4 && empty($this->phq9_opening) && (int) $this->case_type->value === 1 && in_array((int) $this->country_id, [3, 7, 6])) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'phq9_opening' => 0,
                    ],
                ];
            }

            /* HA MORNEAU A CONTACT HOLDER AKKOR KI KELL TOLTNEI A WOS KERDOIVET */
            if ($this->case_company_contract_holder() === 1 && ! $this->wos_survey_clicked && (int) $this->case_type->value === 1) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'wos_survey' => 0,
                    ],
                ];
            }

            /* ÜLÉSEK SZÁMÁNAK VIZSGÁLAT */
            if ($online_therapy_booking) {
                if ($this->consultations()->withTrashed()->count() === 0) {
                    return [
                        'closeable' => 0,
                        'details' => [
                            'consultations' => 0,
                        ], ];
                }
            } elseif ($this->consultations->count() === 0) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'consultations' => 0,
                    ], ];
            }

            /* ELÉGEDETTÉSGI PONTSZÁM VIZSGÁLATA */

            // Az LPP cég esetén az elégedettségi pontszám kitöltése nem szükséges
            if (! $this->customer_satisfaction && $this->company_id != 843) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'customer_satisfaction' => 0,
                    ], ];
            }

            /* PSZICHOLÓGIAI ESETEKNÉL HA A CONTRACT OWNER CPG EUROPE ÉS NINCS KITÖLTVE MIND KÉT WOS KÉRDŐÍV (CSAK AZOKNÁL AZ ESETEKNÉL AMIK ÁPRILIS 3.-ÁN VAGY UTÁNA JÖTTEK LÉTRE) */
            if (
                Carbon::parse($this->created_at)->gte(Carbon::parse('2023-04-03')) &&
                ($this->case_company_contract_holder() === 2 &&
                (int) $this->case_type->value === 1 &&
                $this->wos_answers->count() < 2)) {
                return [
                    'closeable' => 0,
                    'details' => [
                        'wos_survey_cgp' => 0,
                    ],
                ];
            }
        }

        return [
            'closeable' => 1,
        ];
    }

    public function sendQuestionToOperator(): void
    {
        $operator = $this->operator;
        $operator->load('operator_data');
        $expert = Auth::user();
        $country = $this->country;

        try {
            Mail::to($operator->operator_data->company_email)->send(new QuestionToOperator($expert, $this, $operator, $country));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function sendQuestionCopyToExpert($question): void
    {
        $expert = Auth::user();
        $country = $this->country;
        $operator = $this->operator;

        try {
            Mail::to($expert->email)->send(new QuestionCopyToExpert($expert, $this, $operator, $country, $question));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function sendQuestionToCountryEmail($question): void
    {
        $expert = Auth::user();
        $country = $this->country;
        $operator = $this->operator;

        try {
            Mail::to($country->email)->send(new QuestionToCountryEmail($expert, $this, $operator, $country, $question));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function shouldShowCustomerSatisfactionModal(): bool
    {
        // csak akkor, ha pszichológiai esetről van szó ÉS nincs elégedettségi index
        if ($this->case_type->value != 1) {
            return false;
        }

        return ! $this->customer_satisfaction;
    }

    public function inputs()
    {
        return CaseInput::query()->where('company_id', null)->orWhere('company_id', $this->company_id)->orderBy('id', 'asc')->get();
    }

    public function country_name()
    {
        return $this->company->name;
    }

    public function usedPermissions(bool $online_appointment_booking = false): int
    {
        return Consultation::query()->where('case_id', $this->id)
            ->where('permission_id', $this->case_type->value)
            ->when($online_appointment_booking, fn ($query) => $query->withTrashed())
            ->count();
    }

    public function orgId()
    {
        $org_data = DB::table('org_data')->where(['company_id' => $this->company->id, 'country_id' => $this->country_id])->first();

        if (empty($org_data)) {
            return $this->company->orgId;
        }

        return $org_data->org_id;
    }

    public function has_more_consultations(bool $online_appointment_booking = false): bool
    {
        $available_consultations = optional($this->values->where('case_input_id', 21)->first())?->input_value?->value;

        if ($available_consultations - $this->usedPermissions($online_appointment_booking) > 0) {
            return true;
        }

        return $this->values->where('case_input_id', 21)->first() &&
        (int) $this->values->where('case_input_id', 21)->first()->getValue() - $this->usedPermissions($online_appointment_booking) > 0;
    }

    public function case_company_contract_holder()
    {
        $org_datas = optional($this->company)->org_datas;

        if (! $org_datas || $org_datas->isEmpty()) {
            return null;
        }

        $org_data_country = optional($this->company)->org_datas
            ->where('country_id', $this->country_id)
            ->first();

        return $org_data_country?->contract_holder_id;
    }

    // Delete the case entirely with all related data
    public static function delete_case(int $case_id): void
    {
        DB::table('expert_x_case')->where(['case_id' => $case_id])->delete();
        CaseValues::query()->where('case_id', $case_id)->delete();
        Consultation::query()->where('case_id', $case_id)->delete();
        Cases::query()->where('id', $case_id)->delete();
    }

    // Get the first expert in the case with accepted (1 or -1) 1 takes priority over -1
    public function case_accepted_expert(): ?User
    {
        return $this->experts->whereNotIn('pivot.accepted', [CaseExpertStatus::REJECTED->value])->sortByDesc('pivot.accepted')->first();
    }

    public function case_assigned_expert(): ?User
    {
        return $this->experts->sortByDesc('pivot.accepted')->first();
    }

    // Get the first assigned expert for the case (does not matter if they've rejected the case)
    public function first_assigned_expert_by_date(): ?User
    {
        return $this->belongsToMany(User::class, 'expert_x_case', 'case_id', 'user_id')
            ->withPivot('accepted', 'created_at', 'id')
            ->orderByPivot('created_at')
            ->first();
    }

    public function get_deleted_within_48_hour_consultations_count(): int
    {
        return $this->consultations()->onlyTrashed()->get()->filter(fn (Consultation $consultation): bool => $consultation->deleted_at->diffInHours($consultation->created_at) <= 48)->count();
    }

    public static function check_application_code_requirement(int $company_id): bool
    {
        $required = false;

        try {
            $response = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
            ])->get(config('app.eap_online_url').'/api/check-application-code-requirement', [
                'company_id' => $company_id,
            ]);

            if ($response->successful()) {
                $data = json_decode($response->body(), true);
                $required = $data['required'];
            } else {
                Log::info('Failed to check application code requirement for company '.$company_id." : {$response->body()}");
            }
        } catch (Exception $e) {
            Log::info('Failed to check application code requirement for company '.$company_id.' :'.$e->getMessage());
        }

        return $required;
    }

    public static function check_application_code(string $code, int $company_id): bool
    {
        $valid = false;

        try {
            $response = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
            ])->get(config('app.eap_online_url').'/api/check-application-code', [
                'code' => $code,
                'company_id' => $company_id,
            ]);

            if ($response->successful()) {
                $data = json_decode($response->body(), true);
                $valid = $data['valid'];
            } else {
                Log::info('Failed to check application code '.$code." : {$response->body()}");
            }
        } catch (Exception $e) {
            Log::info('Failed to check application code '.$code.' :'.$e->getMessage());
        }

        return $valid;
    }

    public static function store_application_code(string $code, int $case_id): void
    {
        try {
            $response = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.config('app.cgp_internal_authentication_token'),
            ])->post(config('app.eap_online_url').'/api/store-application-code', [
                'code' => $code,
                'case_id' => $case_id,
            ]);

            if (! $response->successful()) {
                Log::info('Failed to check application code '.$code." : {$response->body()}");
            }
        } catch (Exception $e) {
            Log::info('Failed to check application code '.$code.' :'.$e->getMessage());
        }
    }

    public function can_add_more_consultation(bool $online_appointment_booking): bool
    {
        $future_consultation_count = 0;
        Consultation::query()->where('case_id', $this->id)->each(function (Consultation $consultation) use (&$future_consultation_count): void {
            if ($consultation->created_at->isFuture()) {
                $future_consultation_count++;
            }
        });

        if ($online_appointment_booking) {
            return $future_consultation_count < 1;
        }

        return $future_consultation_count < 2;
    }
}
