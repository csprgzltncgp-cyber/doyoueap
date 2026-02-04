<?php

namespace App\Http\Livewire\Admin\ActivityPlanCategoryCase;

use App\Models\ActivityPlan;
use App\Models\ActivityPlanCategory;
use App\Models\ActivityPlanCategoryCase;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Create extends Component
{
    /** Variables used for initilization */
    public ActivityPlanCategory $activity_plan_category;

    public Country $country;

    public Company $company;

    /** @var array<int,mixed> */
    public array $fields;

    /** @var Collection<Country> */
    public Collection $countries;

    /** @var Collection<City> */
    public Collection $cities;

    /** @var Collection<Company> */
    public Collection $companies;

    /** @var Collection<User> */
    public Collection $experts;

    /** @var Collection<User> */
    public Collection $cgp_employees;

    /** Variables used when filling the form */
    public string $title;

    public int $step;

    public int $max_step;

    /** @var array<int,mixed> */
    public array $field_values;

    public function mount(ActivityPlanCategory $activity_plan_category, Country $country, Company $company): void
    {
        /** Variables used for initilization */
        $this->activity_plan_category = $activity_plan_category;
        $this->country = $country;
        $this->company = $company;
        $this->fields = $this->activity_plan_category->fields()->get()->toArray();

        /** Variables used when filling the form */
        $this->step = 0;
        $this->max_step = count($this->fields);
        $this->title = $this->getTitle();

        $this->countries = Country::query()->has('cities')->orderBy('name')->get();
        $this->cities = City::query()->orderBy('name')->get();
        $this->companies = Company::query()->orderBy('name')->get();
        $this->experts = User::query()->where('type', 'expert')->orderBy('name')->get();
        $this->cgp_employees = User::query()->where('type', 'like', '%admin%')->orderBy('name')->get();
        $this->field_values = array_fill_keys(array_column($this->fields, 'id'), null);
    }

    public function render()
    {
        return view('livewire.admin.activity-plan-category-case.create');
    }

    public function save()
    {
        $case = ActivityPlanCategoryCase::query()->create([
            'activity_plan_category_id' => $this->activity_plan_category->id,
            'country_id' => $this->country->id,
            'company_id' => $this->company->id,
        ]);

        if (collect($this->field_values)->contains(null)) {
            $this->emit('validationError', ['message' => __('activity-plan.please-fill-all-fields')]);

            return null;
        }

        foreach ($this->field_values as $field_id => $value) {
            $field = collect($this->fields)->where('id', $field_id)->first();

            $case->activity_plan_category_case_values()->create([
                'activity_plan_category_case_id' => $case->id,
                'activity_plan_category_field_id' => $field['id'],
                'value' => $value,
            ]);
        }

        $activity_plan = ActivityPlan::query()
            ->when(! has_super_access_to_activity_plan(), fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('user_id')
            ->where('company_id', $this->company->id)
            ->first();

        return redirect()->route(auth()->user()->type.'.activity-plan.index', ['activity_plan' => $activity_plan]);
    }

    public function nextStep(): void
    {
        if ($this->step >= $this->max_step) {
            return;
        }

        // The field needs to be filled to continue
        if (empty($this->field_values[$this->fields[$this->step]['id']])) {
            $this->emit('validationError', ['message' => __('validation.required', ['attribute' => $this->fields[$this->step]['name']])]);

            return;
        }

        $this->step++;
        $this->title = $this->getTitle();

        $this->emit('stepChanged');
    }

    public function prevStep(): void
    {
        if ($this->step <= 0) {
            return;
        }

        $this->step--;
        $this->title = $this->getTitle();

        $this->emit('stepChanged');
    }

    public function getTitle()
    {
        if ($this->step === $this->max_step) {
            return __('common.save');
        }

        if (array_key_exists($this->step, $this->fields)) {
            return $this->fields[$this->step]['name'];
        }

        return '';
    }
}
