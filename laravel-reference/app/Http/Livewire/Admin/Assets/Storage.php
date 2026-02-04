<?php

namespace App\Http\Livewire\Admin\Assets;

use App\Exports\Asset\StorageList;
use App\Models\Asset;
use App\Models\AssetOwner;
use App\Models\AssetType;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Storage extends Component
{
    public $types;

    public $perPage = 10;

    public $search = '';

    public $sort = 'asc';

    public $assets;

    public $owners;

    public $current_asset_id;

    public $newOwner;

    public $discard_reason;

    public $recycling_method;

    protected $rules = [
        'assets.*.name' => ['nullable'],
        'assets.*.own_id' => ['nullable'],
        'assets.*.date_of_purchase' => ['required'],
        'assets.*.phone_num' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.pin' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.puk' => ['required_if:assets.*.asset_type_id,!=,14'], // 14 - not and sim card
        'assets.*.provider' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.package' => ['required_if:assets.*.asset_type_id,!=,3,4'], // 3,14 - not mobile phone and sim card
        'assets.*.asset_type_id' => ['required'],
        'assets.*.discard_reason' => ['nullable', 'max:255'],
        'assets.*.recycling_method' => ['nullable', 'max:255'],
    ];

    public function render()
    {
        $this->assets = $this->get_asstes();

        return view('livewire.admin.assets.storage')->extends('layout.master');
    }

    public function mount(): void
    {
        $this->types = AssetType::query()->get();
        $this->owners = AssetOwner::query()->whereNot('id', 900)->orderBy('name')->get();
    }

    public function updated($field): void
    {
        // If own_id exists remove it from the new item
        if (
            Str::contains($field, 'own_id') &&
            Asset::query()
                ->where('own_id', $this->assets[explode('.', (string) $field)[1]]->own_id)
                ->whereNot('id', $this->assets[explode('.', (string) $field)[1]]->id)
                ->exists()) {
            $this->assets[explode('.', (string) $field)[1]]->own_id = null;
        }

        $this->validateOnly($field, $this->rules);

        $this->assets->each->save();
    }

    public function resetSearch(): void
    {
        $this->search = '';
    }

    // Set currenty asset key modal submit
    public function setItemId($key): void
    {
        $this->current_asset_id = $key;
        $this->emit('showModal', $this->current_asset_id);
    }

    // Change asset owner
    public function changeOwner(array $data): void
    {
        $this->newOwner = $this->owners->find($data['owner_id']);
        $this->validate([
            'newOwner' => 'required',
        ]);

        $asset = $this->assets->where('id', $this->current_asset_id)->first();
        $asset->update(['owner_id' => $data['owner_id']]);

        $this->emit('ownerChanged');
        $this->get_asstes();
    }

    // Force delete asset item
    public function delete_asset($asset_id): void
    {
        Asset::query()->where('id', $asset_id)->forceDelete();
        $this->get_asstes();
    }

    // Soft delete asset item (Waste)
    public function discardItem(): void
    {
        $asset = $this->assets->where('id', $this->current_asset_id)->first();
        $asset->update(
            [
                'discard_reason' => $this->discard_reason,
                'recycling_method' => $this->recycling_method,
            ]
        );
        $asset->delete();

        $this->emit('itemDiscarded');
        $this->get_asstes();
    }

    public function export()
    {
        $assets = $this->get_asstes()->map(fn (Asset $asset): array => [
            'inventory_name' => $asset->name,
            'inventory_own_id' => $asset->own_id,
            'inventory_cgp_id' => $asset->cgp_id,
            'inventory_date_of_purchase' => $asset->date_of_purchase,
            'inventory_phone_num' => $asset->phone_num,
            'inventory_pin' => $asset->pin,
            'inventory_puk' => $asset->puk,
            'inventory_provider' => $asset->provider,
            'inventory_package' => $asset->package,
        ])->toArray();

        return Excel::download(new StorageList($assets), 'raktar.xlsx');
    }

    private function get_asstes()
    {
        return Asset::query()
            ->with('owner')
            ->when(! empty($this->search), function ($query): void {
                $query->orWhereHas('type', fn ($query) => $query->where('asset_types.name', 'like', "%{$this->search}%"))
                    ->orWhere('own_id', 'like', "%{$this->search}%")
                    ->orWhere('cgp_id', 'like', "%{$this->search}%")
                    ->orWhere('phone_num', 'like', "%{$this->search}%")
                    ->orWhere('pin', 'like', "%{$this->search}%")
                    ->orWhere('puk', 'like', "%{$this->search}%")
                    ->orWhere('provider', 'like', "%{$this->search}%")
                    ->orWhere('discard_reason', 'like', "%{$this->search}%")
                    ->orWhere('recycling_method', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('package', 'like', "%{$this->search}%")
                    ->withTrashed();
            })
            ->when(empty($this->search), function ($query): void {
                $query->whereHas('owner', fn ($query) => $query->where('asset_owners.id', 900)); // item in Storage
            })
            ->orderBy('name', $this->sort)
            ->get();
    }

    public function emit_delete_popup($asset_id): void
    {
        $this->emit('trigger_delete_popup', $asset_id);
    }

    public function reset_search(): void
    {
        $this->search = '';
    }
}
