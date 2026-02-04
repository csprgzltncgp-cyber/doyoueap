<?php

namespace App\Http\Livewire\Admin\AssetOwner;

use App\Models\AssetOwner;
use App\Models\Country;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Livewire\Component;

class Create extends Component
{
    public $owner;

    protected $rules = [
        'owner.name' => ['required'],
        'owner.country_id' => 'required|exists:countries,id',
    ];

    public function render()
    {
        $countries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();

        return view('livewire.admin.asset-owners.create', ['countries' => $countries]);
    }

    public function mount(): void
    {
        $this->owner = new AssetOwner;
    }

    public function store(): void
    {
        $this->validate();

        $this->owner->save();

        $this->emit('newOwner', $this->owner);
    }
}
