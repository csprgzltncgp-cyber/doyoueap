<?php

namespace App\Http\Livewire\Client\Riport;

use Livewire\Component;

class EapOnlineRiportNumberComponent extends Component
{
    public $text;

    public $value;

    public $allValue;

    public $valueId;

    public $allValueId;

    public function render()
    {
        return view('livewire.client.riport.eap-online-riport-number-component');
    }
}
