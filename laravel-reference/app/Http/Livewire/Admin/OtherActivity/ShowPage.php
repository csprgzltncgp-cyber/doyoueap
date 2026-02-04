<?php

namespace App\Http\Livewire\Admin\OtherActivity;

use App\Enums\OtherActivityStatus;
use App\Mail\ExpertOtherActivityMail;
use App\Mail\ExpertOtherActivityPriceChangeMail;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\DirectInvoiceOtherActivityData;
use App\Models\InvoiceOtherActivityData;
use App\Models\OtherActivity;
use App\Models\OtherActivityEvent;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowPage extends Component
{
    use WithFileUploads;

    public OtherActivity $otherActivity;

    public $currently_editing;

    public $isFreeForCompany;

    protected $listeners = ['deleteOtherActivity' => 'delete', 'refreshComponent' => '$refresh'];

    protected function rules(): array
    {
        return [
            'otherActivity.type' => ['required', 'integer'],
            'otherActivity.permission_id' => ['required', 'integer', 'exists:permissions'],
            'otherActivity.company_id' => ['required', 'integer', 'exists:companies'],
            'otherActivity.contract_holder_id' => ['required', 'integer', 'exists:contract_holders'],
            'otherActivity.user_id' => ['nullable', 'integer', 'exists:users'],
            'otherActivity.country_id' => ['nullable', 'integer', 'exists:countries'],
            'otherActivity.city_id' => ['nullable', 'integer', 'exists:cities'],
            'otherActivity.activity_id' => ['required', 'string'],
            'otherActivity.company_price' => ['nullable', 'integer'],
            'otherActivity.company_currency' => ['nullable', 'string'],
            'otherActivity.company_email' => ['nullable', 'email'],
            'otherActivity.company_phone' => ['nullable', 'string'],
            'otherActivity.user_price' => ['nullable', 'integer'],
            'otherActivity.user_currency' => ['nullable', 'string'],
            'otherActivity.user_phone' => ['nullable', 'string'],
            'otherActivity.language' => ['nullable', 'string'],
            'otherActivity.participants' => ['nullable', 'integer', 'min:0'],
            'otherActivity.date' => ['nullable', 'string'],
            'otherActivity.start_time' => ['nullable', 'string'],
            'otherActivity.end_time' => ['nullable', 'string'],
            'otherActivity.status' => ['integer'],
        ];
    }

    public function mount($id): void
    {
        $this->otherActivity = OtherActivity::query()->where('id', $id)->first();
        $this->isFreeForCompany = empty($this->otherActivity->company_price) ? 1 : 0;
    }

    public function render()
    {
        $permissions = Permission::query()->get();

        return view('livewire.admin.other-activity.show-page', [
            'is_outsorceable' => OtherActivity::query()->where('id', $this->otherActivity->id)->first()->is_outsourceable(),
            'is_outsourced' => OtherActivity::query()->where('id', $this->otherActivity->id)->first()->is_outsourced(),
            'companies' => Company::query()->orderBy('name')->whereHas('countries', fn ($query) => $query->where('id', $this->otherActivity->country_id))->get(),
            'countries' => Country::query()->orderBy('name')->get(),
            'cities' => City::query()->orderBy('name')->when($this->otherActivity->country_id, fn ($query) => $query->where('country_id', $this->otherActivity->country_id))->get(),
            'experts' => User::query()->orderBy('name')
                ->where('type', 'expert')
                ->whereHas('outsource_countries', fn ($query) => $query->where('country_id', $this->otherActivity->country_id))
                ->get(),
            'permissions' => $permissions,
        ])->extends('layout.master');
    }

    public function updatedIsFreeForCompany($value): void
    {
        if (! $value == 0) {
            $this->otherActivity->company_price = null;
            $this->otherActivity->company_currency = null;
            $this->otherActivity->save();
        }
    }

    public function accept_user_price(): void
    {
        OtherActivityEvent::query()->updateOrCreate([
            'other_activity_id' => $this->otherActivity->id,
        ], [
            'type' => OtherActivityEvent::TYPE_OTHER_ACTIVITY_ACCEPTED_BY_ADMIN,
        ]);

        $this->otherActivity->update([
            'status' => OtherActivityStatus::STATUS_IN_PROGRESS,
        ]);

        $this->otherActivity->refresh();
    }

    public function close()
    {
        // IF expert is an affiliate, check if the expert payment is set
        $expert_data = optional(User::query()->where('id', $this->otherActivity->user_id)->first())->expert_data;
        if ($expert_data && ! $expert_data->is_cgp_employee && (is_null($this->otherActivity->user_price) || is_null($this->otherActivity->user_currency))) {
            $this->emit('expert_payment_missing');

            return null;
        }

        if ($this->otherActivity->user_price && is_null($this->otherActivity->user_currency)) {
            $this->emit('expert_currency_missing');

            return null;
        }

        $this->otherActivity->update([
            'status' => OtherActivityStatus::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        $this->otherActivity->event()->delete();

        if (! empty($this->otherActivity->user_price) && ! empty($this->otherActivity->user_currency)) {
            InvoiceOtherActivityData::query()->create([
                'other_activity_id' => $this->otherActivity->id,
                'activity_id' => $this->otherActivity->activity_id,
                'price' => $this->otherActivity->user_price,
                'currency' => $this->otherActivity->user_currency,
                'expert_id' => $this->otherActivity->user_id,
            ]);
        }

        if (! empty($this->otherActivity->company_price)) {
            DirectInvoiceOtherActivityData::query()->create([
                'other_activity_id' => $this->otherActivity->id,
                'company_id' => $this->otherActivity->company_id,
                'country_id' => $this->otherActivity->country_id,
            ]);
        }

        return redirect()->route('admin.other-activities.index');
    }

    public function edit($attribute): void
    {
        $this->currently_editing = $attribute;
    }

    public function save(): void
    {
        if ($this->otherActivity->isDirty('user_id')) {
            $this->emit('expertOutsourced');
            $this->otherActivity->event()->delete();

            $this->otherActivity->load('user');

            if (config('app.env') != 'staging' && $this->otherActivity->user) {
                Mail::to($this->otherActivity->user->email)->send(new ExpertOtherActivityMail($this->otherActivity));
            }
        }

        if ($this->otherActivity->isDirty('status') && $this->otherActivity->status == OtherActivityStatus::STATUS_CLOSED) {
            $this->otherActivity->closed_at = now();
        }

        if ($this->otherActivity->isDirty('user_price') || $this->otherActivity->isDirty('user_currency')) {
            OtherActivityEvent::query()->updateOrCreate([
                'other_activity_id' => $this->otherActivity->id,
            ], [
                'type' => OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_ADMIN,
            ]);

            if (config('app.env') != 'staging' && $this->otherActivity->user) {
                Mail::to($this->otherActivity->user->email)->send(new ExpertOtherActivityPriceChangeMail($this->otherActivity));
            }

            // Set currency for the user price from the user's invoice datas
            $expert = User::query()->where('id', $this->otherActivity->user_id)->first();
            if ($expert && $expert->invoice_datas) {
                $this->otherActivity->user_currency = $expert->invoice_datas->currency;
            }
        }

        if ($this->otherActivity->isDirty('status') && $this->otherActivity->status == OtherActivityStatus::STATUS_CLOSED) {
            InvoiceOtherActivityData::query()->updateOrCreate([
                'other_activity_id' => $this->otherActivity->id,
                'expert_id' => $this->otherActivity->user_id,
            ], [
                'activity_id' => $this->otherActivity->activity_id,
                'price' => $this->otherActivity->user_price,
                'currency' => $this->otherActivity->user_currency,
            ]);
        }

        $this->otherActivity->save();
        $this->otherActivity->refresh();
    }

    public function delete()
    {
        $this->otherActivity->event()->delete();
        $this->otherActivity->delete();

        return redirect()->route('admin.other-activities.index');
    }
}
