<?php

namespace App\Http\Livewire\Client\Riport;

use Livewire\Component;

class EapOnlineRiportNumberBigComponent extends Component
{
    public $text;

    public $value;

    public $quarter;

    public function render()
    {
        return view('livewire.client.riport.eap-online-riport-number-big-component');
    }
}
