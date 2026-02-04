<?php

namespace App\Http\Livewire\Admin\Permission;

use App\Models\Company;
use Livewire\Component;

class Index extends Component
{
    public $companies;

    public $search;

    public $sort = 'asc';

    public function render()
    {
        return view('livewire.admin.permission.index');
    }

    public function mount(): void
    {
        $this->companies = $this->get_companies();
    }

    public function updatedSearch(): void
    {
        $this->companies = $this->get_companies();
    }

    public function updatedSort(): void
    {
        $this->companies = $this->get_companies();

    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->companies = $this->get_companies();
    }

    private function get_companies()
    {
        return $this->companies = Company::query()
            ->when(! empty($this->search), fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name', $this->sort)
            ->get();
    }
}
