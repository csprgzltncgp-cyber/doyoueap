<?php

namespace App\Http\Livewire\Admin\Assets;

use App\Exports\Asset\WasteList;
use App\Models\Asset;
use App\Models\AssetType;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Waste extends Component
{
    public $types;

    public $perPage = 10;

    public $showInventory;

    public $search = '';

    public $sort = 'asc';

    public $assets;

    protected $rules = [
        'assets.*.name' => ['nullable'],
        'assets.*.own_id' => ['nullable'],
        'assets.*.date_of_purchase' => ['required'],
        'assets.*.phone_num' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.pin' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.puk' => ['required_if:assets.*.asset_type_id,!=,14'], // 14 - not sim card
        'assets.*.provider' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.package' => ['required_if:assets.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'assets.*.asset_type_id' => ['required'],
        'assets.*.discard_reason' => ['nullable', 'max:255'],
        'assets.*.recycling_method' => ['nullable', 'max:255'],
    ];

    public function render()
    {
        $this->assets = $this->get_asstes();

        return view('livewire.admin.assets.waste')->extends('layout.master');
    }

    public function mount(): void
    {
        $this->types = AssetType::query()->get();
        $this->showInventory = false;
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

    public function showInventory($row): void
    {
        $this->showInventory = ($this->showInventory == '' || $this->showInventory != $row) ? $row : '';
    }

    public function resetSearch(): void
    {
        $this->search = '';
    }

    // Force delete asset item
    public function deleteItem($itemKey): void
    {
        $this->assets[$itemKey]->forceDelete();
        unset($this->assets[$itemKey]);
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

        return Excel::download(new WasteList($assets), 'selejt.xlsx');
    }

    private function get_asstes()
    {
        return Asset::query()
            ->with('owner')
            ->when(! empty($this->search), function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas('type', function ($query): void {
                        $query->where('asset_types.name', 'like', "%{$this->search}%");
                    })
                    ->orWhere('own_id', 'like', "%{$this->search}%")
                    ->orWhere('cgp_id', 'like', "%{$this->search}%")
                    ->orWhere('phone_num', 'like', "%{$this->search}%")
                    ->orWhere('pin', 'like', "%{$this->search}%")
                    ->orWhere('puk', 'like', "%{$this->search}%")
                    ->orWhere('provider', 'like', "%{$this->search}%")
                    ->orWhere('discard_reason', 'like', "%{$this->search}%")
                    ->orWhere('recycling_method', 'like', "%{$this->search}%")
                    ->orWhere('package', 'like', "%{$this->search}%")
                    ->withTrashed();
            })
            ->when(empty($this->search), function ($query): void {
                $query->onlyTrashed();
            })
            ->orderBy('deleted_at', $this->sort)
            ->get();
    }

    public function reset_search(): void
    {
        $this->search = '';
    }
}
