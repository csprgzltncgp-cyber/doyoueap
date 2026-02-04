<?php

namespace App\Http\Livewire\Admin\Assets;

use App\Models\Asset as AssetModel;
use App\Models\AssetOwner;
use App\Models\AssetType;
use App\Models\Country;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Carbon\Carbon;
use Livewire\Component;

class Owner extends Component
{
    public AssetOwner $owner;

    public $assets;

    public $countries;

    public $show_type = false;

    public $types;

    public $new_asset_type;

    protected $listeners = [
        'refresh_assets',
    ];

    protected $rules = [
        'owner.name' => 'required',
        'owner.country_id' => 'required',
    ];

    public function mount(AssetOwner $owner): void
    {
        $this->owner = $owner;
        $this->owner->load('assets');
        $this->types = AssetType::query()->orderBy('name')->get();

        $this->countries = Country::query()
            ->withoutGlobalScopes([CountryScope::class, LanguageScope::class])
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.assets.owner');
    }

    public function refresh_assets(): void
    {
        $this->owner->load('assets');
    }

    public function updated(): void
    {
        $this->validate();
        $this->owner->save();
    }

    public function save(): void
    {
        $this->emit('asset_data_saved');
    }

    public function new_asset_step_1(): void
    {
        $this->show_type = true;
    }

    public function new_asset_step_2(): void
    {
        if ($this->new_asset_type != null) {
            $new_asset = AssetModel::query()->create(
                [
                    'asset_type_id' => $this->new_asset_type,
                    'owner_id' => $this->owner->id,
                    'date_of_purchase' => Carbon::now()->format('Y-m-d'),
                ]
            );

            $new_asset->update([
                'cgp_id' => 'CGP'.$new_asset->id,
            ]);

            $this->show_type = false;
            $this->owner->load('assets');
            $this->dispatchBrowserEvent('asset_added');
        }
    }
}
