<?php

namespace App\Http\Livewire\Client\Riport;

use App\Models\Country;
use App\Traits\EapOnline\Riport as EapOnlineRiportTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EapOnlineRiportComponent extends Component
{
    use EapOnlineRiportTrait;

    public $riportData;

    public $intervals;

    public $user;

    public $currentInterval;

    public $country;

    public $totalView;

    public $quarter;

    public function render()
    {
        return view('livewire.client.riport.eap-online-riport-component');
    }

    public function mount($country_id, $totalView, $quarter): void
    {
        $this->user = Auth::user();
        $this->totalView = $totalView && has_connected_companies($this->user);
        $this->quarter = $quarter;
        $this->intervals = $this->get_eap_online_riport_intervals();
        $this->currentInterval = collect(data_get($this->intervals, (int) $quarter));
        $this->country = Country::query()->where('id', $country_id)->first();
        try {
            $this->riportData = $this->get_eap_online_riport_data($this->currentInterval['from'], $this->currentInterval['to'], $this->country ?? $this->user->country, (bool) $this->totalView);
        } catch (Exception) {

        }
    }

    public function setCurrentInterval($quarter): void
    {
        $this->currentInterval = $this->intervals[(int) $quarter];

        try {
            $this->riportData = $this->get_eap_online_riport_data($this->currentInterval['from'], $this->currentInterval['to'], $this->country ?? $this->user->country, $this->totalView);
        } catch (Exception) {
        }
    }
}
