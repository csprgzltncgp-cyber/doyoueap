<?php

namespace App\Http\Livewire\Admin\EapOnline;

use App\Models\EapOnline\EapOnlineTherapyChatCountry;
use Livewire\Component;

class OnlineTherapyCountryToggleComponent extends Component
{
    public $country;

    public $checked;

    public function render()
    {
        return view('livewire.admin.eap-online.online-therapy-country-toggle-component');
    }

    public function mount(): void
    {
        if (EapOnlineTherapyChatCountry::query()->where('country_id', $this->country->id)->first()) {
            $this->checked = true;
        } else {
            $this->checked = false;
        }
    }

    public function toggle_video_chat(): void
    {
        $videoChatCountry = EapOnlineTherapyChatCountry::query()->where('country_id', $this->country->id)->first();

        if ($videoChatCountry) {
            $videoChatCountry->delete();
            $this->checked = false;
        } else {
            $videoChatCountry = new EapOnlineTherapyChatCountry;
            $videoChatCountry->country_id = $this->country->id;
            $videoChatCountry->save();
            $this->checked = true;
        }
    }
}
