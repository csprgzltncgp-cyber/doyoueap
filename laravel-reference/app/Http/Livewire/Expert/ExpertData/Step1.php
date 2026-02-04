<?php

namespace App\Http\Livewire\Expert\ExpertData;

use Livewire\Component;

class Step1 extends Component
{
    public function render()
    {
        return view('livewire.expert.expert-data.step1')->extends('layout.master');
    }
}
