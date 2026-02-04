<?php

namespace App\Http\Livewire\Admin\AssetOwner;

use App\Models\AssetOwner;
use App\Models\Country;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Livewire\Component;

class Edit extends Component
{
    public $owner;

    protected $rules = [
        'owner.name' => ['required', 'min:3'],
        'owner.country_id' => 'required|exists:countries,id',
    ];

    public function render()
    {
        $countries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();

        return view('livewire.admin.asset-owners.edit', ['countries' => $countries]);
    }

    public function mount(AssetOwner $owner): void
    {
        $this->owner = $owner;
    }

    public function updated(): void
    {
        $this->validate();
        $this->owner->save();
        $this->emit('ownerDataUpdated', $this->owner->id, $this->owner->name);
    }

    public function update(): void
    {
        $this->validate();
        $this->owner->save();

        $this->emit('ownerDataUpdated', $this->owner->id, $this->owner->name);
    }
}
