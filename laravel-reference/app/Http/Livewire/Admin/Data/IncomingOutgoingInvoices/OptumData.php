<?php

namespace App\Http\Livewire\Admin\Data\IncomingOutgoingInvoices;

use App\Enums\ContractHolderCompany;
use App\Enums\ContractHolderEnum;
use App\Traits\DashboardData\IncomingOutgoingInvoices\SimpleDataCollect;
use Carbon\Carbon;
use Livewire\Component;

class OptumData extends Component
{
    use SimpleDataCollect;

    public $data;

    public $show_data = false;

    public $filter_year;

    public $filter_month;

    public function mount(): void
    {
        $this->filter_year = Carbon::now()->format('Y');
        $this->filter_month = Carbon::now()->subMonthsNoOverflow()->format('m');
    }

    public function render()
    {
        return view('livewire.admin.data.incoming-outgoing-invoices.simple-data', [
            'name' => 'Optum',
        ]);
    }

    public function updatedShowData($opened): void
    {
        if (! $opened) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::OPTUM),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month, ContractHolderCompany::OPTUM)
        );
    }

    public function updatedFilterYear(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::OPTUM),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month, ContractHolderCompany::OPTUM)
        );
    }

    public function updatedFilterMonth(): void
    {
        if (! $this->show_data) {
            return;
        }

        $this->data = $this->merge_datas(
            $this->get_incoming_datas($this->filter_year, $this->filter_month, ContractHolderEnum::OPTUM),
            $this->get_outgoing_datas($this->filter_year, $this->filter_month, ContractHolderCompany::OPTUM)
        );
    }
}
