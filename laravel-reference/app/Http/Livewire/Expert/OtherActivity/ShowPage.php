<?php

namespace App\Http\Livewire\Expert\OtherActivity;

use App\Enums\OtherActivityStatus;
use App\Models\OtherActivity;
use App\Models\OtherActivityEvent;
use App\Models\Permission;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowPage extends Component
{
    use WithFileUploads;

    public $otherActivity;

    public $currently_editing;

    protected function rules(): array
    {
        return [
            'otherActivity.user_currency' => ['nullable', 'string'],
            'otherActivity.user_price' => ['nullable', 'integer'],
            'otherActivity.participants' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function mount($id): void
    {
        $this->otherActivity = OtherActivity::query()
            ->where('id', $id)
            ->with(['country', 'company', 'city'])
            ->first();
    }

    public function render()
    {
        $permissions = Permission::query()->get();

        return view('livewire.expert.other-activity.show-page', ['permissions' => $permissions])->extends('layout.master');
    }

    public function accept(): void
    {
        $this->otherActivity->event()->delete();
        $this->otherActivity->update([
            'status' => OtherActivityStatus::STATUS_IN_PROGRESS,
        ]);

        $this->otherActivity->refresh();
    }

    public function deny()
    {
        $this->otherActivity->update([
            'user_id' => null,
            'user_phone' => null,
        ]);

        OtherActivityEvent::query()->updateOrCreate([
            'other_activity_id' => $this->otherActivity->id,
        ], [
            'type' => OtherActivityEvent::TYPE_OTHER_ACTIVITY_DENIED_BY_EXPERT,
        ]);

        return redirect()->route('expert.other-activity.index');
    }

    public function edit($attribute): void
    {
        $this->currently_editing = $attribute;
    }

    public function save(): void
    {
        if ($this->otherActivity->isDirty('user_price') || $this->otherActivity->isDirty('user_currency')) {
            OtherActivityEvent::query()->updateOrCreate([
                'other_activity_id' => $this->otherActivity->id,
            ], [
                'type' => OtherActivityEvent::TYPE_OTHER_ACTIVITY_PRICE_MODIFIED_BY_EXPERT,
            ]);
        }

        $this->otherActivity->save();
        $this->otherActivity->refresh();
    }
}
