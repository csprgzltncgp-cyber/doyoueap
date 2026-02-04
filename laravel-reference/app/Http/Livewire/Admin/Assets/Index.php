<?php

namespace App\Http\Livewire\Admin\Assets;

use App\Exports\Asset\AssetList;
use App\Models\Asset;
use App\Models\AssetOwner;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    public $search = '';

    public $sort = 'asc';

    public $show_inventory;

    public $owners;

    public $filtered_assets;

    protected $listeners = [
        'refresh_assets',
    ];

    public function mount(): void
    {
        $this->owners = AssetOwner::query()->orderBy('name')->where('id', '!=', 900)->get(); // Get assets where owner is not storage (900)
    }

    public function render()
    {
        return view('livewire.admin.assets.index')->extends('layout.master');
    }

    // Do search only when search field changes
    public function updatedSearch(): void
    {
        $this->filtered_assets = Asset::query()
            ->with('owner')
            ->when(! empty($this->search), function ($query): void {
                $query->where('name', 'like', "%{$this->search}%");
                $query->orWhere('own_id', 'like', "%{$this->search}%");
                $query->orWhere('cgp_id', 'like', "%{$this->search}%");
                $query->orWhere('phone_num', 'like', "%{$this->search}%");
                $query->orWhere('pin', 'like', "%{$this->search}%");
                $query->orWhere('puk', 'like', "%{$this->search}%");
                $query->orWhere('provider', 'like', "%{$this->search}%");
                $query->orWhere('discard_reason', 'like', "%{$this->search}%");
                $query->orWhere('recycling_method', 'like', "%{$this->search}%");
                $query->orWhere('package', 'like', "%{$this->search}%");
                $query->orWhereHas('owner', function ($query): void {
                    $query->where('name', 'like', "%{$this->search}%");
                });
                $query->orWhereHas('type', function ($query): void {
                    $query->where('name', 'like', "%{$this->search}%");
                });
                $query->withTrashed();
            })
            ->orderBy('name', $this->sort)
            ->get();
    }

    public function updatedSort(): void
    {
        $this->updatedSearch();
    }

    public function reset_search(): void
    {
        $this->search = '';
        $this->updatedSearch();
    }

    public function export()
    {
        $assets = ($this->search == '') ? Asset::query()->orderBy('name')->get() : $this->filtered_assets;

        $exports = $assets->map(fn ($asset): array => [
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

        return Excel::download(new AssetList($exports), 'leltár.xlsx');
    }

    public function show_inventory(int $owner_id): void
    {
        $this->show_inventory = ($this->show_inventory == '' || $this->show_inventory != $owner_id) ? $owner_id : '';
    }

    public function delete_owner($owner_id): void
    {
        AssetOwner::query()->where('id', $owner_id)->delete();
        $this->owners = AssetOwner::query()->whereNot('id', 900)->get(); // Where not 900 - storage
    }

    public function refresh_assets(): void
    {
        $this->updatedSearch();
    }
}
