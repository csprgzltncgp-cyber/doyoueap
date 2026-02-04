<?php

namespace App\Http\Livewire\Admin\Data;

use App\Models\UsedConsultations;
use Livewire\Component;

class ConsultationUsage extends Component
{
    public $consultation_datas;

    public $show_data;

    public function render()
    {
        return view('livewire.admin.data.consultation-usage');
    }

    public function mount(): void
    {
        $this->show_data = false;
    }

    public function show_data(): void
    {
        $this->show_data = ! $this->show_data;
        $data = new UsedConsultations;
        $this->consultation_datas = $data->calculate_consultation_datas();
    }
}
