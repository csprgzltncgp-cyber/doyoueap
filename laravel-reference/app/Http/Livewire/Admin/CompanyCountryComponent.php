<?php

namespace App\Http\Livewire\Admin;

use App\Enums\UserTypeEnum;
use App\Models\ActivityPlan;
use App\Models\ContractDateReminderEmail;
use App\Models\ContractHolder;
use App\Models\Country;
use App\Models\CrisisIntervention;
use App\Models\OrgData;
use App\Models\User;
use App\Models\Workshop;
use App\Traits\ActivityIdPrefixTrait;
use App\Traits\ContractDateTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CompanyCountryComponent extends Component
{
    use ActivityIdPrefixTrait;
    use ContractDateTrait;

    public $company;

    public $country;

    public $countryDifferentiates;

    public $contract_holder;

    public $org_id;

    public $contract_date;

    public $contract_date_end;

    public $workshops;

    public $crisis_interventions;

    public $clientUser;

    public $head_count;

    public $is_workshops_opened = false;

    public $is_crisis_interventions_opened = false;

    public $is_opened = false;

    public $contract_date_reminder_email;

    public $companyConnected;

    public $activity_plan_user;

    public ActivityPlan $activity_plan;

    // Data not used in view
    public $org_data;

    protected $listeners = [
        'refreshModels' => 'getWorkshopsAndCrisisInterventions',
        'closeAll' => 'closeAll',
        '$refresh',
        'setClientUserToNull' => 'setClientUserToNull',
        'setCountryClientNewPassword' => 'setClientUserNewPassword',
        'setContractDateReminderEmailToNull' => 'setContractDateReminderEmailToNull',
        'setContractDateToNull' => 'setContractDateToNull',
    ];

    protected $rules = [
        'clientUser.username' => ['sometimes', 'required'],
        'clientUser.password' => ['sometimes', 'required'],
        'clientUser.language_id' => ['sometimes', 'required'],
        'clientUser.all_country' => ['sometimes', 'required'],
        'contract_date_reminder_email' => ['email'],
    ];

    public function mount($country_id): void
    {
        $this->clientUser = $this->company->clientUsers()->where('country_id', $country_id)->first();

        $this->activity_plan = ActivityPlan::query()->where('company_id', $this->company->id)->firstOrCreate([
            'company_id' => $this->company->id,
            'country_id' => $country_id,
        ]);

        $this->activity_plan_user = optional($this->activity_plan->user)->id;

        $this->country = Country::query()->find($country_id);
        $this->org_data = OrgData::query()->where([
            'country_id' => $country_id,
            'company_id' => $this->company->id,
        ])->first();

        $this->org_id = $this->org_data->org_id ?? null;
        $this->head_count = $this->org_data->head_count ?? null;

        if ($this->contract_date || $this->contract_holder != 2) {
            $this->getWorkshopsAndCrisisInterventions();
        } else {
            $this->workshops = collect([]);
            $this->crisis_interventions = collect([]);
        }
    }

    public function render()
    {
        $this->contract_holder = $this->org_data->contract_holder_id ?? null;
        $this->contract_date = $this->org_data->contract_date ?? null;
        $this->contract_date_end = $this->org_data->contract_date_end ?? null;
        $this->contract_date_reminder_email = optional(ContractDateReminderEmail::query()->where(['country_id' => $this->country->id, 'company_id' => $this->company->id])->first())->value;
        $account_admins = User::query()->where('type', UserTypeEnum::ACCOUNT_ADMIN->value)->orderBy('name')->get();

        return view('livewire.admin.company-country-component', [
            'contractHolders' => ContractHolder::all(),
            'account_admins' => $account_admins,
        ]);
    }

    public function updated($propertyName, $value): void
    {
        if ($propertyName == 'activity_plan_user') {
            $this->activity_plan->user_id = $value;
            $this->activity_plan->save();

            return;
        }

        if (! in_array(explode('.', (string) $propertyName)[0], ['workshops', 'crisis_interventions', 'clientUser', 'contract_date_reminder_email'])) {
            $this->updateOrgData($propertyName);
        }

        if (explode('.', (string) $propertyName)[0] === 'clientUser') {
            $field = explode('.', (string) $propertyName)[1];
            $user = $this->company->clientUsers()->where('country_id', $this->country->id)->first();

            if ($user) {
                $user->update([
                    $field => ($field === 'password') ? Hash::make($value) : $value,
                ]);
            } else {
                $data = [
                    $field => ($field === 'password') ? Hash::make($value) : $value,
                    'all_country' => 0,
                    'country_id' => $this->country->id,
                    'email' => strtolower((string) $this->country->code).'@cgpeu.com',
                    'language_id' => 1,
                    'type' => 'client',
                ];

                $user = User::query()->make();
                $user->fill($data);
                $user->save();
            }

            $this->company->clientUsers()->syncWithoutDetaching($user);
        }

        if (explode('.', (string) $propertyName)[0] === 'contract_date_reminder_email') {
            $email = ContractDateReminderEmail::query()->where(['country_id' => $this->country->id, 'company_id' => $this->company->id])->first();

            if ($email) {
                $email->update([
                    'value' => $value,
                ]);
            } else {
                $data = [
                    'value' => $value,
                    'company_id' => $this->company->id,
                    'country_id' => $this->country->id,
                ];

                $email = ContractDateReminderEmail::query()->make();
                $email->fill($data);
                $email->save();
            }
        }

        if ($propertyName === 'clientUser.language_id') {
            $quarters = [1, 2, 3, 4, null]; // 4 quarters and null as possible value

            foreach ($quarters as $quarter) {
                Cache::forget('riport-'.$this->company->id.'--total');
                Cache::forget('riport-'.$this->company->id.'-'.$quarter.'-total');
                Cache::forget('riport-'.$quarter.'-'.$this->country->id.'-'.$this->company->id);
            }
        }
    }

    public function getWorkshopsAndCrisisInterventions(): void
    {
        $this->workshops = Workshop::query()
            ->where('country_id', $this->country->id)
            ->where('company_id', $this->company->id)
            ->when(optional($this->org_data)->contract_date, function (Builder $query): void {
                $query->whereBetween('created_at', [
                    $this->getPeriodStart($this->org_data->contract_date),
                    $this->getPeriodEnd($this->org_data->contract_date),
                ]);
            })
            ->get();

        $this->crisis_interventions = CrisisIntervention::query()
            ->where('country_id', $this->country->id)
            ->where('company_id', $this->company->id)
            ->when(optional($this->org_data)->contract_date, function (Builder $query): void {
                $query->whereBetween('created_at', [
                    $this->getPeriodStart($this->org_data->contract_date),
                    $this->getPeriodEnd($this->org_data->contract_date),
                ]);
            })
            ->get();
    }

    public function addWorkshop(): void
    {
        if (empty($this->contract_date) && $this->contract_holder == 2) {
            $this->emit('showError', __('common.contract_date_required'));

            return;
        }

        $last_workshop_id = Workshop::query()->max('id');
        $prefix = $this->getActivityIdPref($this->contract_holder);

        Workshop::query()->create([
            'activity_id' => 'w'.($prefix.($last_workshop_id + 1)),
            'company_id' => $this->company->id,
            'country_id' => $this->country->id,
            'free' => 1,
            'contract_holder_id' => $this->contract_holder,
            'active' => 1,
        ]);

        $this->getWorkshopsAndCrisisInterventions();

        OrgData::query()
            ->where('company_id', $this->company->id)
            ->where('country_id', $this->country->id)
            ->update([
                'workshops_number' => is_countable($this->workshops) ? count($this->workshops) : 0,
            ]);
    }

    public function addCrisisIntervention(): void
    {
        if (empty($this->contract_date) && $this->contract_holder == 2) {
            $this->emit('showError', __('common.contract_date_required'));

            return;
        }

        $last_crisis_id = CrisisIntervention::query()->max('id');
        $prefix = $this->getActivityIdPref($this->contract_holder);

        CrisisIntervention::query()->create([
            'activity_id' => 'ci'.($prefix.($last_crisis_id + 1)),
            'company_id' => $this->company->id,
            'country_id' => $this->country->id,
            'free' => 1,
            'contract_holder_id' => $this->contract_holder,
            'active' => 1,
        ]);

        $this->getWorkshopsAndCrisisInterventions();

        OrgData::query()
            ->where('company_id', $this->company->id)
            ->where('country_id', $this->country->id)
            ->update([
                'crisis_number' => is_countable($this->crisis_interventions) ? count($this->crisis_interventions) : 0,
            ]);
    }

    public function toggleOpen($propertyName): void
    {
        $this->{$propertyName} = ! $this->{$propertyName};
    }

    public function closeAll(): void
    {
        $this->is_workshops_opened = false;
        $this->is_crisis_interventions_opened = false;
        $this->is_opened = false;
    }

    public function setClientUserToNull(): void
    {
        $this->clientUser = null;
    }

    public function setContractDateReminderEmailToNull(): void
    {
        $this->contract_date_reminder_email = null;
    }

    public function setClientUserNewPassword($country_id, $password): void
    {
        if ($country_id == $this->country->id) {
            $this->updated('clientUser.password', $password);
        }
    }

    public function setContractDateToNull(): void
    {
        $this->contract_date = null;
        $this->contract_date_end = null;
    }

    private function updateOrgData($column_name): void
    {
        if (empty($this->org_data)) {
            $this->org_data = OrgData::query()->create([
                $column_name => $this->{$column_name},
                'company_id' => $this->company->id,
                'country_id' => $this->country->id,
            ]);
        } else {
            $this->org_data->update([
                $column_name => $this->{$column_name},
            ]);
        }
    }
}
