<?php

namespace App\Http\Livewire\Admin\AssetType;

use App\Models\AssetType;
use Livewire\Component;

class Create extends Component
{
    public $types;

    public $type;

    public $addNew = false;

    public $name;

    protected $rules = [
        'name' => ['required'],
    ];

    public function render()
    {
        return view('livewire.admin.asset-types.create')->extends('layout.master');
    }

    // Set default values
    public function mount(AssetType $types): void
    {
        $this->types = $types->get();
    }

    public function store(AssetType $types): void
    {
        $this->type = new AssetType([
            'name' => $this->name,
        ]);

        $this->validate();
        $this->type->save();
        $this->types = $types->get();

        $this->addNew = false;
        $this->type = null;
        $this->name = '';

        $this->emit('successEvent', 'Új típus létrehozva!');
    }

    public function addType(): void
    {
        $this->addNew = true;
    }
}
