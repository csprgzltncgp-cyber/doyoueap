<?php

namespace App\Http\Livewire\Admin\Assets;

use App\Models\Asset;
use App\Models\AssetOwner;
use App\Models\AssetType;
use App\Models\Country;
use App\Scopes\CountryScope;
use App\Scopes\LanguageScope;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public Asset $asset;

    public AssetOwner $owner;

    public AssetOwner $owners;

    public $inventoryItems;

    protected $listeners = ['newOwner'];

    public $owner_id;

    public $types;

    public $type_name = 'Eszköz';

    public $itemsType;

    public $type_id;

    public $index = 0;

    public $showType = false;

    public $itemCount;

    protected $rules = [
        'inventoryItems.*.name' => ['required'],
        'inventoryItems.*.asset_type_id' => ['required'],
        'inventoryItems.*.own_id' => ['required'],
        'inventoryItems.*.date_of_purchase' => ['required'],
        'inventoryItems.*.phone_num' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'inventoryItems.*.pin' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3 - not mobile phone
        'inventoryItems.*.puk' => ['required_if:inventoryItems.*.asset_type_id,!=,14'], // 14 - not sim card
        'inventoryItems.*.provider' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'inventoryItems.*.package' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
        'owner.name' => ['required'],
        'owner.country_id' => ['required'],
    ];

    public function render()
    {
        $countries = Country::query()->withoutGlobalScopes([CountryScope::class, LanguageScope::class])->orderBy('name')->get();

        return view('livewire.admin.assets.create', ['countries' => $countries])->extends('layout.master');
    }

    // Set default properties
    public function mount(AssetType $types): void
    {
        $this->asset = new Asset;
        $this->owner = new AssetOwner;
        $this->inventoryItems = $this->owner->assets;
        $this->types = $types->get();
    }

    // Create new owner
    public function storeOwner(): void
    {
        try {
            $this->validate([
                'owner.name' => ['required'],
                'owner.country_id' => ['required'],
            ]);
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);

            return;
        }

        $this->owner->save();
        $this->showType = true;
    }

    // Creat new asset items
    public function storeItem(): void
    {
        if ((is_countable($this->inventoryItems) ? count($this->inventoryItems) : 0) == 0) {
            $this->emit('inventoryEmpty');

            return;
        }

        try {
            $this->validate([
                'inventoryItems.*.name' => ['required'],
                'inventoryItems.*.asset_type_id' => ['required'],
                'inventoryItems.*.own_id' => ['required'],
                'inventoryItems.*.date_of_purchase' => ['required'],
                'inventoryItems.*.phone_num' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
                'inventoryItems.*.pin' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
                'inventoryItems.*.puk' => ['required_if:inventoryItems.*.asset_type_id,!=,14'], // 14 - not sim card
                'inventoryItems.*.provider' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
                'inventoryItems.*.package' => ['required_if:inventoryItems.*.asset_type_id,!=,3,14'], // 3,14 - not mobile phone and sim card
                'owner.name' => ['required'],
                'owner.country_id' => ['required'],
            ]);
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);
            $this->validate();

            return;
        }

        $this->owner->assets()->saveMany($this->inventoryItems);
        $this->emit('inventoryDataSaved');
    }

    // Change asset type
    public function changeType($typeId): void
    {
        $this->type_id = $typeId;
        $types = new AssetType;
        $this->type_name = $types->where(['id' => $this->type_id])->first()->name;
        $this->itemsType[$this->index - 1]['type_id'] = $this->type_id;
        $this->itemsType[$this->index - 1]['type_name'] = $this->type_name;

        if ($this->type_id != null) {
            $this->addItem();
            $this->showType = false;
        }
    }

    // Set owner id from InventoryOwner component
    public function newOwner(AssetOwner $owner): void
    {
        $this->owners = $owner;
        $this->inventoryItems = $this->owners->assets;
    }

    // Show asset type select
    public function showType(): void
    {
        // Validate owner
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->emit('errorEvent', collect($e->errors())->first()[0]);
            $this->validate();

            return;
        }

        if ($this->owner->id == null) {
            $this->storeOwner();
        }

        $this->showType = true;
        $this->type_id = null;
        $this->itemCount = $this->inventoryItems->count() + 1;
        $this->index++;
    }

    // Add new item to asset
    public function addItem(): void
    {
        $item = $this->owner->assets()->create(['asset_type_id' => $this->type_id]);
        $item->cgp_id = 'CGP'.$item->id;
        $item->save();
        $this->inventoryItems[] = $item;

        // Emit event to create datepicker input for the item's date_of_purchase.
        $this->emit('addDatePicker', $this->index - 1);
    }

    public function deleteItem($index): void
    {
        $this->inventoryItems[$index]->forceDelete(); // Asset is set to soft delete. Use forceDelete to permenantly delete object.
        $this->index--;
        unset($this->inventoryItems[$index]);
    }
}
