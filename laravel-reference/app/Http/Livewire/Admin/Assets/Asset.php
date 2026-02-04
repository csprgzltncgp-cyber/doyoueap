<?php

namespace App\Http\Livewire\Admin\Assets;

use App\Models\Asset as AssetModel;
use App\Models\AssetType;
use Livewire\Component;

class Asset extends Component
{
    public AssetModel $asset;

    public int $asset_index;

    public $types;

    public $discard_reason;

    public $recycling_method;

    public $is_search_result;

    protected $rules = [
        'asset.name' => ['required', 'min:3'],
        'asset.own_id' => ['required'],
        'asset.date_of_purchase' => ['required'],
        'asset.phone_num' => ['required_if:asset.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'asset.pin' => ['required_if:asset.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'asset.puk' => ['required_if:asset.asset_type_id,!=,14'], // 14 - not sim card
        'asset.provider' => ['required_if:asset.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'asset.package' => ['required_if:asset.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'asset.asset_type_id' => ['required'],
    ];

    public function mount(AssetModel $asset, int $index, bool $is_search_result = true): void
    {
        $this->asset = $asset;
        $this->asset_index = $index;
        $this->is_search_result = $is_search_result;
        $this->types = AssetType::query()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.assets.asset');
    }

    public function updated(): void
    {
        $this->validate();
        $this->asset->save();
    }

    public function to_storage(): void
    {
        $this->asset->update([
            'owner_id' => 900,
        ]);
        $this->emitUp('refresh_assets');
    }

    public function discard_asset(): void
    {
        $asset_id = $this->asset->id;

        $this->asset->update([
            'discard_reason' => $this->discard_reason,
            'recycling_method' => $this->recycling_method,
        ]);

        $this->asset->delete();

        $this->emit('close_discard_modal_'.$asset_id);
        $this->emitUp('refresh_assets');
    }

    public function delete_asset(): void
    {
        $this->asset->forceDelete();
        $this->emitUp('refresh_assets');
    }

    public function emit_delete_popup(): void
    {
        $this->emit('trigger_delete_popup_'.$this->asset->id);
    }

    public function trigger_storage_popup(): void
    {
        $this->emit('trigger_storage_popup_'.$this->asset->id);
    }
}
